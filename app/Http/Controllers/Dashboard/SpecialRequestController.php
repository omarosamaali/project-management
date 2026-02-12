<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SpecialRequest;
use App\Models\User;
use App\Models\Requests;
use Illuminate\Http\Request;
use App\Models\SpecialRequestPartner;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectStage;
use App\Models\Requests as ProjectRequest;
use App\Models\ProjectNote;
use App\Models\ProjectPayment;
use Illuminate\Support\Facades\Storage;
use App\Models\Support;
use App\Models\RequestPayment;
use App\Models\RequestStage;
use App\Models\RequestActivity;

class SpecialRequestController extends Controller
{
    public function storeStage(Request $request, $id)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'details'     => 'nullable|string',
            'hours_count' => 'nullable|integer|min:0',
            'end_date'    => 'nullable|date',
        ]);

        RequestStage::create([
            'request_id'  => $id,
            'title'       => $validated['title'],
            'details'     => $validated['details'],
            'hours_count' => $validated['hours_count'] ?? 0,
            'end_date'    => $validated['end_date'],
            'status'      => 'waiting',
        ]);

        // 2. تحديث حالة الطلب (نستخدم الموديل المرتبط بجدول requests)
        // ملاحظة: تأكد من عمل import للموديل في أعلى الصفحة أو استخدم المسار الكامل
        $order = Requests::find($id);

        if ($order) {
            $order->update([
                'status' => 'جاري العمل به'
            ]);
        }

        return back()->with('success', 'تم إضافة المرحلة وتحويل حالة الطلب إلى (جاري العمل به)');
    }

    public function storeStage1(Request $request, $id)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'details'     => 'nullable|string',
            'hours_count' => 'nullable|integer|min:0',
            'end_date'    => 'nullable|date',
        ]);

        // 1. إنشاء المرحلة
        ProjectStage::create([
            'special_request_id'  => $id,
            'title'       => $validated['title'],
            'details'     => $validated['details'],
            'hours_count' => $validated['hours_count'] ?? 0,
            'end_date'    => $validated['end_date'],
            'status'      => 'waiting',
        ]);

        // 2. جلب الطلب وتحديث حالته فوراً بدون شروط معقدة لضمان العمل
        $specialRequest = SpecialRequest::findOrFail($id);

        // سنقوم بتحديث الحالة إلى active مباشرة بمجرد إضافة مرحلة
        $specialRequest->update([
            'status' => 'active',
            'is_project' => 1 // لضمان أنه تم اعتباره مشروعاً بما أن له مراحل
        ]);

        return back()->with('success', 'تم إضافة المرحلة وتنشيط المشروع بنجاح');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $specialRequests = SpecialRequest::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(8)
            ->withQueryString();
        return view('dashboard.special-request.index', compact('specialRequests'));
    }

    public function show(SpecialRequest $SpecialRequest, Request $request)
    {
        $assignedPartnerIds = $SpecialRequest->partners()->pluck('partner_id')->toArray();
        $requiredServiceType = $SpecialRequest->project_type;
        $partners = User::where('role', 'partner')->whereNotIn('id', $assignedPartnerIds)->get();
        $managers = User::where('role', 'partner')->where('is_employee', 1)->get();

        $collection1 = Support::where('request_id', $SpecialRequest->id)
            ->with(['user', 'unreadMessages', 'messages'])
            ->get()
            ->map(function ($item) {
                $item->is_technical = false;
                return $item;
            });

        $collection2 = \DB::table('technical_support')
            ->where('request_id', $SpecialRequest->id)
            ->orWhere(function ($q) use ($SpecialRequest) {
                $q->where('client_id', $SpecialRequest->client_id)
                    ->where('system_id', $SpecialRequest->system_id);
            })
            ->get()
            ->map(function ($item) {
                $item->is_technical = true;
                return $item;
            });

        $allSupports = $collection1->concat($collection2)->sortByDesc('created_at');

        $allMessages = $SpecialRequest->messages() // نستخدم العلاقة اللي عرفناها فوق
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

   
        return view('dashboard.special-request.show', [
            'SpecialRequest' => $SpecialRequest,
            'supports'       => $allMessages,
            // 'supports'       => $allSupports,
            'partners'       => $partners,
            'managers'       => $managers,
        ]);
    }

    public function assignPartners(Request $request, SpecialRequest $specialRequest)
    {
        // 1. التحقق الأولي من وجود شركاء مختارين
        $hasPartners = $request->has('partner_id') && !empty($request->partner_id);

        if (!$hasPartners) {
            return back()->withErrors([
                'partner_id' => 'يرجى اختيار شريك واحد على الأقل وتحديد نسبة الأرباح.'
            ])->withInput();
        }

        // 2. التحقق من صحة البيانات (Validation)
        $request->validate([
            'partner_id'   => 'required|array|min:1',
            'partner_id.*' => 'required|exists:users,id',
            'share_type'   => 'required|array',
            'share_type.*' => 'required|in:percentage,fixed',
            'notes'        => 'nullable|string|max:1000',
        ], [
            'partner_id.required' => 'يرجى اختيار شريك واحد على الأقل.',
            'share_type.*.in'     => 'نوع الإسناد غير صحيح.',
        ]);

        // 3. التحقق المنطقي من القيم (السماح بالصفر)
        foreach ($request->partner_id as $partnerId) {
            $type = $request->share_type[$partnerId] ?? 'percentage';

            if ($type === 'percentage') {
                $value = $request->profit_share_percentage[$partnerId] ?? null;
                // التحقق: يسمح بالصفر ولا يسمح بالحقول الفارغة أو السالبة أو فوق 100
                if ($value === null || $value === '' || $value < 0 || $value > 100) {
                    return back()->withErrors([
                        'profit_share_percentage' => 'نسبة الأرباح يجب أن تكون بين 0 و 100 للشريك المختار.'
                    ])->withInput();
                }
            } else {
                $value = $request->fixed_amount[$partnerId] ?? null;
                // التحقق: يسمح بالصفر ولا يسمح بالحقول الفارغة أو السالبة
                if ($value === null || $value === '' || $value < 0) {
                    return back()->withErrors([
                        'fixed_amount' => 'المبلغ الثابت يجب أن يكون 0 أو أكثر للشريك المختار.'
                    ])->withInput();
                }
            }
        }

        // 4. حساب مجموع النسب المئوية للتأكد من عدم تجاوز 100%
        $currentTotalShare = $specialRequest->partners
            ->where('pivot.share_type', 'percentage')
            ->sum('pivot.profit_share_percentage');

        $newPercentageTotal = 0;
        foreach ($request->partner_id as $partnerId) {
            if (($request->share_type[$partnerId] ?? 'percentage') === 'percentage') {
                $newPercentageTotal += (int) ($request->profit_share_percentage[$partnerId] ?? 0);
            }
        }

        if ($currentTotalShare + $newPercentageTotal > 100) {
            $grandTotal = $currentTotalShare + $newPercentageTotal;
            return back()->withErrors([
                'profit_share_percentage' => "خطأ: مجموع النسب المئوية ({$grandTotal}%) يتجاوز 100%. المسند حالياً: {$currentTotalShare}%."
            ])->withInput();
        }

        // 5. تنفيذ الإسناد داخل Transaction
        try {
            DB::transaction(function () use ($request, $specialRequest) {
                $isProject = $request->boolean('is_project');
                $hasStages = $specialRequest->stages()->count() > 0;
                $currentStatus = $specialRequest->status;

                if ($isProject) {
                    $currentStatus = $hasStages ? 'active' : 'بانتظار عروض الاسعار';
                }

                $specialRequest->update([
                    'is_project' => $isProject,
                    'status'     => $currentStatus,
                ]);

                foreach ($request->partner_id as $partnerId) {
                    // تخطي الشريك إذا كان مضافاً مسبقاً لهذا الطلب
                    if ($specialRequest->partners()->where('partner_id', $partnerId)->exists()) {
                        continue;
                    }

                    $type = $request->share_type[$partnerId] ?? 'percentage';

                    SpecialRequestPartner::create([
                        'order_number'            => 'REQ-' . $specialRequest->id . '-' . time() . rand(100, 999),
                        'special_request_id'      => $specialRequest->id,
                        'partner_id'              => $partnerId,
                        'share_type'              => $type,
                        'profit_share_percentage' => $type === 'percentage' ? (int)$request->profit_share_percentage[$partnerId] : 0,
                        'fixed_amount'            => $type === 'fixed' ? (float)$request->fixed_amount[$partnerId] : 0,
                        'notes'                   => $request->notes,
                    ]);

                    \App\Models\ProjectActivity::create([
                        'special_request_id' => $specialRequest->id,
                        'user_id'            => auth()->id(),
                        'type'               => 'file',
                        'description'        => 'تم إسناد شريك جديد للمشروع وتحديث حالة الطلب',
                    ]);
                }
            });

            return redirect()->route('dashboard.special-request.show', $specialRequest)
                ->with('success', 'تم إسناد الشركاء وتحديث حالة المشروع بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'حدث خطأ أثناء إسناد الشركاء: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function requestAssignPartners(Request $request, $id)
    {
        $specialRequest = ProjectRequest::findOrFail($id);

        // 1. التحقق من اختيار شريك
        if (!$request->has('partner_id') || empty($request->partner_id)) {
            return back()->withErrors(['partner_id' => 'يرجى اختيار شريك واحد على الأقل.'])->withInput();
        }

        // 2. التحقق من صحة مدخلات المصفوفة
        $request->validate([
            'partner_id'   => 'required|array',
            'partner_id.*' => 'exists:users,id',
            'share_type'   => 'required|array',
            'share_type.*' => 'required|in:percentage,fixed',
        ]);

        // 3. التحقق المنطقي من القيم (السماح بالصفر)
        foreach ($request->partner_id as $partnerId) {
            $type = $request->share_type[$partnerId] ?? 'percentage';

            if ($type === 'percentage') {
                $value = $request->profit_share_percentage[$partnerId] ?? null;
                // التحقق: يسمح بـ 0، يرفض الفارغ، يرفض السالب، يرفض فوق 100
                if ($value === null || $value === '' || $value < 0 || $value > 100) {
                    return back()->withErrors([
                        'profit_share_percentage' => 'نسبة الأرباح يجب أن تكون بين 0 و 100.'
                    ])->withInput();
                }
            } else {
                $value = $request->fixed_amount[$partnerId] ?? null;
                // التحقق: يسمح بـ 0، يرفض الفارغ، يرفض السالب
                if ($value === null || $value === '' || $value < 0) {
                    return back()->withErrors([
                        'fixed_amount' => 'المبلغ الثابت يجب أن يكون 0 أو أكثر.'
                    ])->withInput();
                }
            }
        }

        // 4. حساب مجموع النسب المئوية
        $currentTotalShare = DB::table('special_request_partner')
            ->where('request_id', $specialRequest->id)
            ->where('share_type', 'percentage')
            ->sum('profit_share_percentage');

        $newPercentageTotal = 0;
        foreach ($request->partner_id as $partnerId) {
            if (($request->share_type[$partnerId] ?? 'percentage') === 'percentage') {
                $newPercentageTotal += (int) ($request->profit_share_percentage[$partnerId] ?? 0);
            }
        }

        if ($currentTotalShare + $newPercentageTotal > 100) {
            $grandTotal = $currentTotalShare + $newPercentageTotal;
            return back()->withErrors([
                'profit_share_percentage' => "مجموع النسب المؤوية ({$grandTotal}%) يتجاوز 100%. المسند حالياً: {$currentTotalShare}%."
            ])->withInput();
        }

        // 5. الحفظ داخل Transaction
        try {
            DB::transaction(function () use ($request, $specialRequest) {

                $specialRequest->update([
                    'is_project' => $request->boolean('is_project'),
                    'status'     => 'in_progress',
                ]);

                foreach ($request->partner_id as $partnerId) {
                    $type = $request->share_type[$partnerId] ?? 'percentage';

                    $exists = DB::table('special_request_partner')
                        ->where('request_id', $specialRequest->id)
                        ->where('partner_id', $partnerId)
                        ->exists();

                    if (!$exists) {
                        SpecialRequestPartner::create([
                            'order_number'            => 'REQ-' . $specialRequest->id . '-' . time() . rand(10, 99),
                            'request_id'              => $specialRequest->id,
                            'partner_id'              => $partnerId,
                            'share_type'              => $type,
                            'profit_share_percentage' => $type === 'percentage' ? (int) $request->profit_share_percentage[$partnerId] : 0,
                            'fixed_amount'            => $type === 'fixed' ? (float) $request->fixed_amount[$partnerId] : 0,
                            'notes'                   => $request->notes,
                        ]);

                        RequestActivity::create([
                            'request_id'  => $specialRequest->id,
                            'user_id'     => auth()->id(),
                            'type'        => 'system',
                            'description' => $type === 'percentage'
                                ? 'تم إسناد شريك جديد بنسبة ' . ($request->profit_share_percentage[$partnerId] ?? 0) . '%'
                                : 'تم إسناد شريك جديد بمبلغ ثابت ' . number_format((float) ($request->fixed_amount[$partnerId] ?? 0), 2) . ' جنيه',
                        ]);
                    }
                }
            });
            return redirect()->route('dashboard.requests.show', $specialRequest->id)
                ->with('success', 'تمت عملية الإسناد وتخزين الشركاء بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()])->withInput();
        }
    }

    public function removePartner(SpecialRequest $specialRequest, User $partner)
    {
        $assignment = SpecialRequestPartner::where('special_request_id', $specialRequest->id)
            ->where('partner_id', $partner->id)
            ->first();
        if (!$assignment) {
            return back()->with('error', 'هذا الشريك غير مُسند لهذا المشروع');
        }
        $assignment->delete();
        return back()->with('success', 'تم إلغاء إسناد الشريك من المشروع بنجاح');
    }

    public function removePartnerRequest(Requests $specialRequest, $partnerId) // استبدل User $partner بـ $partnerId
    {
        // ابحث عن الشريك يدوياً
        $partner = User::findOrFail($partnerId);

        $assignment = SpecialRequestPartner::where('request_id', $specialRequest->id) // تأكد من اسم العمود في قاعدة البيانات
            ->where('partner_id', $partner->id)
            ->first();

        if (!$assignment) {
            return back()->with('error', 'هذا الشريك غير مُسند لهذا المشروع');
        }
        $assignment->delete();
        return back()->with('success', 'تم إلغاء إسناد الشريك من المشروع بنجاح');
    }

    public function addStage(Request $request, SpecialRequest $specialRequest)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'hours_count' => 'nullable|numeric|min:0',
            'end_date' => 'nullable|date',
        ]);

        $specialRequest->stages()->create(array_merge($data, ['status' => 'waiting']));

        $specialRequest->update(['status' => 'active']);

        \App\Models\ProjectActivity::create([
            'special_request_id' => $specialRequest->id,
            'user_id' => auth()->id(),
            'type' => 'stage_added',
            'description' => "تم إضافة مرحلة جديدة: {$data['title']} وتحديث حالة المشروع إلى جاري العمل به",
        ]);

        return back()->with('success', 'تمت إضافة المرحلة بنجاح وتنشيط المشروع');
    }

    public function updateStage(Request $request, ProjectStage $stage)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            // 'hours_count' => 'required|numeric|min:0',
            'end_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $stage->update($data);
        return back()->with('success', 'تم تحديث المرحلة بنجاح');
    }

    public function updateRequestStage(Request $request, RequestStage $stage)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            // 'hours_count' => 'required|numeric|min:0',
            'end_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $stage->update($data);
        return back()->with('success', 'تم تحديث المرحلة بنجاح');
    }

    public function destroyStage(ProjectStage $stage)
    {
        if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403);
        }
        if ($stage->tasks()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف هذه المرحلة لأنها تحتوي على ' . $stage->tasks()->count() . ' مهمة مرتبطة بها. قم بحذف المهام أولاً.');
        }

        $stage->delete();

        return back()->with('success', 'تم حذف المرحلة بنجاح');
    }

    public function destroyRequestStage(RequestStage $stage)
    {
        if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403);
        }
        if ($stage->tasks()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف هذه المرحلة لأنها تحتوي على ' . $stage->tasks()->count() . ' مهمة مرتبطة بها. قم بحذف المهام أولاً.');
        }

        $stage->delete();

        return back()->with('success', 'تم حذف المرحلة بنجاح');
    }

    public function getProjectCompletionPercentage(SpecialRequest $specialRequest)
    {
        $stages = $specialRequest->stages;

        if ($stages->count() == 0) {
            return 0;
        }

        $totalPercentage = 0;

        foreach ($stages as $stage) {
            $totalPercentage += $stage->completion_percentage;
        }

        return round($totalPercentage / $stages->count(), 2);
    }

    public function destroy(SpecialRequest $request)
    {
        $request->delete();
        return redirect()->route('dashboard.special-request.index')->with('success', 'تم حذف الطلب بنجاح');
    }

    public function addNote(Request $request, SpecialRequest $special_request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $special_request->notes()->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'user_id' => auth()->id(),
            'visible_to_client' => $request->has('visible_to_client'),
        ]);

        \App\Models\ProjectActivity::create([
            'special_request_id' => $special_request->id,
            'user_id' => auth()->id(),
            'type' => 'file',
            'description' => 'تم إضافة ملاحظة جديدة للمشروع: ' . $data['title'],
        ]);

        return back()->with('success', 'تمت إضافة الملاحظة بنجاح');
    }

    public function updateNote(Request $request, ProjectNote $note)
    {
        if (auth()->user()->role !== 'admin' && auth()->id() !== $note->user_id) {
            abort(403, 'غير مصرح لك بتعديل هذه الملاحظة');
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'visible_to_client' => 'nullable|boolean',
        ]);

        $data['visible_to_client'] = $request->has('visible_to_client') ? true : false;

        $note->update($data);

        return back()->with('success', 'تم تحديث الملاحظة بنجاح');
    }

    public function destroyNote(ProjectNote $note)
    {
        if (auth()->user()->role !== 'admin' && auth()->id() !== $note->user_id) {
            abort(403, 'غير مصرح لك بحذف هذه الملاحظة');
        }

        $note->delete();

        return back()->with('success', 'تم حذف الملاحظة بنجاح');
    }


    public function requestAddNote(Request $request, Requests $specialRequest)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'visible_to_client' => 'nullable|boolean',
        ]);

        $data['user_id'] = auth()->id();
        $data['visible_to_client'] = $request->has('visible_to_client') ? true : false;

        $specialRequest->notes()->create($data);
        RequestActivity::create([
            'request_id' => $specialRequest->id,
            'user_id' => auth()->id(),
            'type' => 'file',
            'description' => 'تم اضافة ملاحظة جديدة للمشروع',
        ]);

        return back()->with('success', 'تمت إضافة الملاحظة بنجاح');
    }

    public function requestUpdateNote(Request $request, ProjectNote $note)
    {
        if (auth()->user()->role !== 'admin' && auth()->id() !== $note->user_id) {
            abort(403, 'غير مصرح لك بتعديل هذه الملاحظة');
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'visible_to_client' => 'nullable|boolean',
        ]);

        $data['visible_to_client'] = $request->has('visible_to_client') ? true : false;

        $note->update($data);

        return back()->with('success', 'تم تحديث الملاحظة بنجاح');
    }

    public function requestDestroyNote(ProjectNote $note)
    {
        if (auth()->user()->role !== 'admin' && auth()->id() !== $note->user_id) {
            abort(403, 'غير مصرح لك بحذف هذه الملاحظة');
        }

        $note->delete();

        return back()->with('success', 'تم حذف الملاحظة بنجاح');
    }

    public function toggleNoteVisibility(ProjectNote $note)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $note->update([
            'visible_to_client' => !$note->visible_to_client
        ]);

        return back()->with('success', 'تم تحديث حالة الظهور بنجاح');
    }

    public function updateProjectBudget(Request $request, SpecialRequest $specialRequest)
    {
        $request->validate([
            'is_project' => 'nullable|boolean',
            'price' => 'required|numeric|min:0',
            'payment_type' => 'required|in:single,installments',
            'installments' => 'nullable|array',
            'installments.*.name' => 'required_with:installments|string',
            'installments.*.amount' => 'required_with:installments|numeric|min:0',
            'installments.*.due_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request, $specialRequest) {
            $specialRequest->update([
                'is_project' => $request->boolean('is_project'),
                'price' => $request->price,
                'payment_type' => $request->payment_type,
            ]);

            RequestPayment::where('special_request_id', $specialRequest->id)->delete();

            if ($request->payment_type == 'single') {
                RequestPayment::create([
                    'special_request_id' => $specialRequest->id,
                    'payment_name' => 'الدفعة الكاملة',
                    'amount' => $request->price,
                    'status' => 'unpaid'
                ]);
            } else {
                if ($request->has('installments') && is_array($request->installments)) {
                    foreach ($request->installments as $installment) {
                        RequestPayment::create([
                            'special_request_id' => $specialRequest->id,
                            'payment_name' => $installment['name'],
                            'amount' => $installment['amount'],
                            'due_date' => $installment['due_date'] ?? null,
                            'status' => 'unpaid'
                        ]);
                    }
                }
            }
        });

        return back()->with('success', 'تم تحديث ميزانية المشروع بنجاح');
    }

    public function uploadPaymentProof(Request $request, ProjectPayment $payment)
    {
        if (auth()->user()->role !== 'client') {
            abort(403);
        }

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'payment_notes' => 'nullable|string|max:500',
        ]);

        $file = $request->file('payment_proof');
        $path = $file->store('payment_proofs', 'public');

        $payment->update([
            'payment_proof' => $path,
            'payment_notes' => $request->payment_notes,
            'status' => 'pending',
        ]);

        return back()->with('success', 'تم رفع إثبات الدفع بنجاح، في انتظار مراجعة الإدارة');
    }

    public function confirmPayment(ProjectPayment $payment)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'تم تأكيد الدفعة بنجاح');
    }

    public function rejectPayment(Request $request, ProjectPayment $payment)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'rejection_notes' => 'required|string|max:500',
        ]);

        $payment->update([
            'status' => 'unpaid',
            'payment_notes' => 'مرفوض: ' . $request->rejection_notes,
            'payment_proof' => null,
        ]);

        return back()->with('success', 'تم رفض الدفعة');
    }

    public function updateProjectStatus(Request $request, SpecialRequest $specialRequest)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
        $request->validate([
            'is_project'       => 'required|boolean',
            'bidding_deadline' => 'nullable|date',
        ]);
        $isProject = $request->is_project;
        $updateData = [
            'is_project' => $isProject,
        ];
        if ($isProject) {
            $updateData['bidding_deadline'] = $request->bidding_deadline;
            $hasStages = $specialRequest->stages()->count() > 0;
            if ($hasStages) {
                $updateData['status'] = 'active';
            } else {
                $updateData['status'] = 'بانتظار عروض الاسعار';
            }
        } else {
            $updateData['bidding_deadline'] = null;
        }
        $specialRequest->update($updateData);
        $message = $isProject
            ? 'تم تحويل الطلب إلى مشروع وتحديث الحالة بنجاح'
            : 'تم إلغاء تحويل الطلب إلى مشروع';
        return back()->with('success', $message);
    }
    public function updateRequestStatus(Request $request, Requests $specialRequest)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
        $request->validate([
            'is_project'       => 'required|boolean',
            'bidding_deadline' => 'nullable|date',
        ]);
        $isProject = $request->is_project;
        $updateData = [
            'is_project' => $isProject,
        ];
        if ($isProject) {
            $updateData['bidding_deadline'] = $request->bidding_deadline;
            $hasStages = $specialRequest->stages()->count() > 0;
            if ($hasStages) {
                $updateData['status'] = 'جاري العمل به';
            } else {
                $updateData['status'] = 'بانتظار عروض الاسعار';
            }
        } else {
            $updateData['bidding_deadline'] = null;
        }
        $specialRequest->update($updateData);
        $message = $isProject
            ? 'تم تحويل الطلب إلى مشروع وتحديث الحالة بنجاح'
            : 'تم إلغاء تحويل الطلب إلى مشروع';
        return back()->with('success', $message);
    }

    
    public function deliverProject($id)
    {
        $request = SpecialRequest::findOrFail($id);

        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'غير مسموح لك');
        }

        $request->status = 'in_review';
        $request->save();

        return redirect()->back()->with('success', 'تم تسليم المشروع للعميل للمراجعة');
    }

    public function receiveProject($id)
    {
        $request = SpecialRequest::findOrFail($id);

        if (auth()->id() !== $request->user_id) {
            return back()->with('error', 'هذا الطلب لا يخصك');
        }

        $request->update([
            'status' => 'completed'
        ]);

        return back()->with('success', 'تم تأكيد الاستلام بنجاح، يسعدنا التعامل معك!');
    }
}
