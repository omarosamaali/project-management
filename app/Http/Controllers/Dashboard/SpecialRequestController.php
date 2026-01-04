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

        return back()->with('success', 'تم إضافة المرحلة بنجاح');
    }


    // Index Method
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

    // Show Method
    public function show(SpecialRequest $SpecialRequest, Request $request)
    {
        // 1. جلب الشركاء والموظفين
        $assignedPartnerIds = $SpecialRequest->partners()->pluck('partner_id')->toArray();
        $requiredServiceType = $SpecialRequest->project_type;
        $partners = User::where('role', 'partner')->whereNotIn('id', $assignedPartnerIds)->get();
        $managers = User::where('role', 'partner')->where('is_employee', 1)->get();

        // 2. جلب البيانات من الجدول الأول (Support) مع تمييزها
        $collection1 = Support::where('request_id', $SpecialRequest->id)
            ->with(['user', 'unreadMessages', 'messages'])
            ->get()
            ->map(function ($item) {
                $item->is_technical = false; // سجل تابع لجدول الـ Support العادي
                return $item;
            });

        // 3. جلب البيانات من الجدول الثاني (technical_support) مع تمييزها
        $collection2 = \DB::table('technical_support')
            ->where('request_id', $SpecialRequest->id)
            ->orWhere(function ($q) use ($SpecialRequest) {
                $q->where('client_id', $SpecialRequest->client_id)
                    ->where('system_id', $SpecialRequest->system_id);
            })
            ->get()
            ->map(function ($item) {
                $item->is_technical = true; // سجل تابع لجدول الدعم التقني المخصص
                return $item;
            });

        // 4. الدمج والترتيب
        $allSupports = $collection1->concat($collection2)->sortByDesc('created_at');

        return view('dashboard.special-request.show', [
            'SpecialRequest' => $SpecialRequest,
            'supports'       => $allSupports,
            'partners'       => $partners,
            'managers'       => $managers,
        ]);
    }
    
    // Assign Partners Method
    public function assignPartners(Request $request, SpecialRequest $specialRequest)
    {
        // التحقق من بيانات الشركاء
        $hasPartners = $request->has('partner_id') && !empty($request->partner_id);

        if ($hasPartners) {
            $request->validate([
                'partner_id' => 'required|array|min:1',
                'partner_id.*' => 'required|exists:users,id',
                'profit_share_percentage' => 'required|array',
                // نتحقق من وجود القيمة لكل شريك تم اختياره فعلياً
                'profit_share_percentage.*' => 'required_with:partner_id.*|integer|min:1|max:100',
                'notes' => 'nullable|string|max:1000',
                                'is_project' => 'nullable|boolean',
            ], [
                'partner_id.required' => 'يرجى اختيار شريك واحد على الأقل.',
                'partner_id.*.exists' => 'الشريك المحدد غير موجود.',
                'profit_share_percentage.required' => 'يرجى تحديد نسبة الأرباح لكل شريك.',
                'profit_share_percentage.*.required' => 'يرجى تحديد نسبة الأرباح لكل شريك.',
                'profit_share_percentage.*.min' => 'نسبة الأرباح يجب أن تكون على الأقل 1%.',
                'profit_share_percentage.*.max' => 'نسبة الأرباح يجب ألا تتجاوز 100%.',
            ]);

            // التحقق من أن مجموع النسب لا يتجاوز 100%
            $currentTotalShare = $specialRequest->partners->sum('pivot.profit_share_percentage');
            $newTotalShare = 0;

            foreach ($request->partner_id as $partnerId) {
                $newTotalShare += (int) $request->profit_share_percentage[$partnerId];
            }

            $grandTotal = $currentTotalShare + $newTotalShare;

            if ($grandTotal > 100) {
                return back()->withErrors([
                    'profit_share_percentage' => "خطأ: مجموع نسب الأرباح ({$grandTotal}%) يتجاوز 100%. المسند حالياً: {$currentTotalShare}%. المجموع الجديد: {$newTotalShare}%."
                ])->withInput();
            }
        } else {
            return back()->withErrors([
                'partner_id' => 'يرجى اختيار شريك واحد على الأقل وتحديد نسبة الأرباح.'
            ])->withInput();
        }

        try {
            DB::transaction(function () use ($request, $specialRequest, $hasPartners) {

                // تحديث حالة المشروع
                $specialRequest->update([
                    'is_project' => $request->boolean('is_project'),
                ]);

                // إضافة الشركاء الجدد
                if ($hasPartners) {
                    foreach ($request->partner_id as $partnerId) {
                        // التحقق مما إذا كان الشريك مسنداً مسبقاً لتجنب التكرار
                        if (!$specialRequest->partners()->where('partner_id', $partnerId)->exists()) {
                            SpecialRequestPartner::create([
                                'order_number' => 'REQ-' . $specialRequest->id . '-' . time() . rand(100, 999),
                                'special_request_id' => $specialRequest->id,
                                'partner_id' => $partnerId,
                                'profit_share_percentage' => $request->profit_share_percentage[$partnerId],
                                'notes' => $request->notes
                            ]);
                            \App\Models\ProjectActivity::create([
                                'special_request_id' => $specialRequest->id,
                                'user_id' => auth()->id(),
                                'type' => 'file', // أو status, invoice, etc
                                'description' => 'تم إسناد شريك جديد للمشروع',
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('dashboard.special-request.show', $specialRequest)
                ->with('success', 'تم إسناد الشركاء للمشروع بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'حدث خطأ أثناء إسناد الشركاء: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function requestAssignPartners(Request $request, $id)
    {
        // البحث عن الموديل باستخدام الـ ID لضمان الدقة
        $specialRequest = ProjectRequest::findOrFail($id);

        // 1. التحقق من وجود شركاء
        if (!$request->has('partner_id') || empty($request->partner_id)) {
            return back()->withErrors(['partner_id' => 'يرجى اختيار شريك واحد على الأقل.'])->withInput();
        }

        // 2. التحقق من صحة البيانات والنسب
        $request->validate([
            'partner_id' => 'required|array',
            'partner_id.*' => 'exists:users,id',
            'profit_share_percentage' => 'required|array',
            'profit_share_percentage.*' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::transaction(function () use ($request, $specialRequest) {

                // تحديث الحقول الأساسية في موديل Requests
                // تأكد أن 'is_project' موجود في الـ fillable داخل موديل Requests
                $specialRequest->update([
                    'is_project' => $request->boolean('is_project'),
                    'status' => 'in_progress' // اختيارياً: تغيير الحالة عند الإسناد
                ]);

                foreach ($request->partner_id as $partnerId) {
                    // الحصول على النسبة الخاصة بهذا الشريك تحديداً
                    $percentage = $request->profit_share_percentage[$partnerId] ?? 0;

                    // التحقق من عدم التكرار باستخدام request_id
                    $exists = DB::table('special_request_partner')
                        ->where('request_id', $specialRequest->id)
                        ->where('partner_id', $partnerId)
                        ->exists();

                    if (!$exists) {
                        // التخزين الفعلي
                        SpecialRequestPartner::create([
                            'order_number' => 'REQ-' . $specialRequest->id . '-' . now()->timestamp . rand(10, 99),
                            'request_id'   => $specialRequest->id,
                            'special_request_id' => $specialRequest->id, // توحيد الـ IDs
                            'partner_id'   => $partnerId,
                            'profit_share_percentage' => $percentage,
                            'notes'        => $request->notes
                        ]);

                        // تسجيل النشاط
                        RequestActivity::create([
                            'request_id' => $specialRequest->id,
                            'user_id' => auth()->id(),
                            'type' => 'system',
                            'description' => 'تم إسناد شريك جديد للمشروع بنسبة ' . $percentage . '%',
                        ]);
                    }
                }
            });

            return redirect()->route('dashboard.requests.show', $specialRequest->id)
                ->with('success', 'تمت عملية الإسناد وتخزين الشركاء بنجاح.');
        } catch (\Exception $e) {
            // في حالة وجود خطأ، سيظهر لك هنا في رسالة حمراء
            return back()->withErrors(['error' => 'عذراً، حدث خطأ أثناء الحفظ: ' . $e->getMessage()])->withInput();
        }
    }

    // Remove Partner Method
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

    public function addStage(Request $request, SpecialRequest $specialRequest)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'hours_count' => 'required|numeric|min:0', // إضافة هذا
            'end_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $specialRequest->stages()->create($data);

        // تسجيل النشاط
        \App\Models\ProjectActivity::create([
            'special_request_id' => $specialRequest->id,
            'user_id' => auth()->id(),
            'type' => 'stage_added',
            'description' => "تم إضافة مرحلة جديدة: {$data['title']}",
        ]);

        return back()->with('success', 'تمت إضافة المرحلة بنجاح');
    }

    public function updateStage(Request $request, ProjectStage $stage)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'hours_count' => 'required|numeric|min:0', // إضافة هذا
            'end_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $stage->update($data);
        return back()->with('success', 'تم تحديث المرحلة بنجاح');
    }

    // Destroy Stage
    public function destroyStage(ProjectStage $stage)
    {
        if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403);
        }

        $stage->delete();

        return back()->with('success', 'تم حذف المرحلة بنجاح');
    }

    // Get Project Completion Percentage
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

        // نسبة إنجاز المشروع = مجموع نسب إنجاز المراحل ÷ عدد المراحل
        return round($totalPercentage / $stages->count(), 2);
    }

    // Destroy Method
    public function destroy(SpecialRequest $request)
    {
        $request->delete();
        return redirect()->route('dashboard.special-request.index')->with('success', 'تم حذف الطلب بنجاح');
    }

    public function addNote(Request $request, SpecialRequest $specialRequest)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'visible_to_client' => 'nullable|boolean',
        ]);

        $data['user_id'] = auth()->id();
        $data['visible_to_client'] = $request->has('visible_to_client') ? true : false;

        $specialRequest->notes()->create($data);
        \App\Models\ProjectActivity::create([
            'special_request_id' => $specialRequest->id,
            'user_id' => auth()->id(),
            'type' => 'file', // أو status, invoice, etc
            'description' => 'تم اضافة ملاحظة جديدة للمشروع',
        ]);

        return back()->with('success', 'تمت إضافة الملاحظة بنجاح');
    }

    // تحديث ملاحظة
    public function updateNote(Request $request, ProjectNote $note)
    {
        // التحقق من الصلاحيات
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

    // حذف ملاحظة
    public function destroyNote(ProjectNote $note)
    {
        // التحقق من الصلاحيات (الأدمن أو صاحب الملاحظة)
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
            'type' => 'file', // أو status, invoice, etc
            'description' => 'تم اضافة ملاحظة جديدة للمشروع',
        ]);

        return back()->with('success', 'تمت إضافة الملاحظة بنجاح');
    }

    // تحديث ملاحظة
    public function requestUpdateNote(Request $request, ProjectNote $note)
    {
        // التحقق من الصلاحيات
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

    // حذف ملاحظة
    public function requestDestroyNote(ProjectNote $note)
    {
        // التحقق من الصلاحيات (الأدمن أو صاحب الملاحظة)
        if (auth()->user()->role !== 'admin' && auth()->id() !== $note->user_id) {
            abort(403, 'غير مصرح لك بحذف هذه الملاحظة');
        }

        $note->delete();

        return back()->with('success', 'تم حذف الملاحظة بنجاح');
    }

    // تبديل ظهور الملاحظة للعميل (للأدمن فقط)
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

    // تحديث بيانات المشروع والميزانية
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
            // تحديث بيانات الطلب
            $specialRequest->update([
                'is_project' => $request->boolean('is_project'),
                'price' => $request->price,
                'payment_type' => $request->payment_type,
            ]);

            // حذف جميع الدفعات القديمة بشكل صحيح
            RequestPayment::where('special_request_id', $specialRequest->id)->delete();

            // إضافة الدفعات الجديدة
            if ($request->payment_type == 'single') {
                // إنشاء دفعة واحدة
                RequestPayment::create([
                    'special_request_id' => $specialRequest->id,
                    'payment_name' => 'الدفعة الكاملة',
                    'amount' => $request->price,
                    'status' => 'unpaid'
                ]);
            } else {
                // إنشاء دفعات متعددة
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

    // رفع إثبات الدفع (للعميل)
    public function uploadPaymentProof(Request $request, ProjectPayment $payment)
    {
        // التحقق من أن المستخدم هو العميل
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
            'status' => 'pending', // قيد المراجعة
        ]);

        return back()->with('success', 'تم رفع إثبات الدفع بنجاح، في انتظار مراجعة الإدارة');
    }

    // تأكيد الدفع (للأدمن)
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

    // رفض الدفع (للأدمن)
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

    // تحويل الطلب إلى مشروع
    public function updateProjectStatus(Request $request, SpecialRequest $specialRequest)
    {
        // التأكد من صلاحية الأدمن
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        // التحقق من البيانات المرسلة
        $request->validate([
            'is_project'       => 'required|boolean',
            'bidding_deadline' => 'nullable|date', // الحقل الجديد
        ]);

        // تجهيز البيانات للتحديث
        $updateData = [
            'is_project' => $request->is_project,
        ];

        // منطق التعامل مع تاريخ عروض الأسعار
        if ($request->is_project) {
            // إذا تم تحويله لمشروع، نحدث التاريخ (سواء تم إدخاله أو بقى null)
            $updateData['bidding_deadline'] = $request->bidding_deadline;
        } else {
            // إذا تم إرجاعه لطلب عادي، نقوم بمسح تاريخ العروض تلقائياً
            $updateData['bidding_deadline'] = null;
        }

        // تنفيذ التحديث
        $specialRequest->update($updateData);

        // تحديد رسالة النجاح
        $message = $request->is_project
            ? 'تم تحويل الطلب إلى مشروع وتحديد موعد العروض بنجاح'
            : 'تم إلغاء تحويل الطلب إلى مشروع ومسح موعد العروض';

        return back()->with('success', $message);
    }

    // في SpecialRequestController.php
    public function deliverProject($id)
    {
        $request = SpecialRequest::findOrFail($id);

        // التحقق من الصلاحية
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'غير مسموح لك');
        }

        // تحديث الحالة
        $request->status = 'in_review';
        $request->save(); // استخدم save للتأكد من التحديث

        return redirect()->back()->with('success', 'تم تسليم المشروع للعميل للمراجعة');
    }

    // 2. العميل يؤكد الاستلام النهائي
    public function receiveProject($id)
    {
        $request = SpecialRequest::findOrFail($id);

        if (auth()->id() !== $request->user_id) {
            return back()->with('error', 'هذا الطلب لا يخصك');
        }

        // هنا فقط تتحول الحالة إلى completed (مكتمل نهائياً)
        $request->update([
            'status' => 'completed'
        ]);

        return back()->with('success', 'تم تأكيد الاستلام بنجاح، يسعدنا التعامل معك!');
    }
}
