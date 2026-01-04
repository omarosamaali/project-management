<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Requests;
use App\Models\System;
use App\Models\User;
use App\Models\Performance;
use App\Models\PartnerSystem;
use App\Models\SpecialRequestPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SpecialRequest;
use App\Models\RequestStage;
use App\Models\RequestsPayment;
use App\Models\RequestNote;
use App\Models\Support;

class RequestsController extends Controller
{
    // Index Method
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();
        $statusFilter = $request->get('status');
        if ($user->role == 'admin') {
            $partnerSystemIds = PartnerSystem::where('partner_id', Auth::id())
                ->pluck('system_id')
                ->toArray();
            $allRequestsCount = Requests::whereIn('system_id', $partnerSystemIds)->count();
            $newRequestsCount = Requests::where('status', 'جديد')
                ->whereIn('system_id', $partnerSystemIds)
                ->count();
            $underProcessRequestsCount = Requests::where('status', 'تحت الاجراء')
                ->whereIn('system_id', $partnerSystemIds)
                ->count();
            $pendingRequestsCount = Requests::where('status', 'معلقة')
                ->whereIn('system_id', $partnerSystemIds)
                ->count();
            $closedRequestsCount = Requests::where('status', 'منتهية')
                ->whereIn('system_id', $partnerSystemIds)
                ->count();
            $requests = Requests::whereIn('system_id', $partnerSystemIds)
                ->with(['system', 'client'])
                ->orderBy('created_at', 'desc')
                ->get();
            $specialRequests = null;
        } elseif ($user->role == 'partner') {
            $partnerSystemIds = PartnerSystem::where('partner_id', Auth::id())
                ->pluck('system_id')
                ->toArray();
            $allRequestsCount = Requests::whereIn('system_id', $partnerSystemIds)->count()
                + SpecialRequestPartner::where('partner_id', Auth::id())->count();
            $newRequestsCount = Requests::where('status', 'جديد')
                ->whereIn('system_id', $partnerSystemIds)->count()
                + SpecialRequestPartner::where('partner_id', Auth::id())->where('status', 'جديد')->count();
            $underProcessRequestsCount = Requests::where('status', 'تحت الاجراء')
                ->whereIn('system_id', $partnerSystemIds)->count()
                + SpecialRequestPartner::where('partner_id', Auth::id())->where('status', 'تحت الاجراء')->count();
            $pendingRequestsCount = Requests::where('status', 'معلقة')
                ->whereIn('system_id', $partnerSystemIds)->count()
                + SpecialRequestPartner::where('partner_id', Auth::id())->where('status', 'معلقة')->count();
            $closedRequestsCount = Requests::where('status', 'منتهية')
                ->whereIn('system_id', $partnerSystemIds)->count()
                + SpecialRequestPartner::where('partner_id', Auth::id())->where('status', 'منتهية')->count();
            $requests = Requests::whereIn('system_id', $partnerSystemIds)
                ->with(['system', 'client'])
                ->orderBy('created_at', 'desc')
                ->get();
            $specialRequests = SpecialRequestPartner::where('partner_id', Auth::id())
                ->with(['specialRequest.user', 'specialRequest.partners', 'partner'])
                ->get();
        } else {
            $specialRequests = null;

            $allRequests = Requests::query();
            $allRequestsCount = Requests::where('client_id', Auth::user()->id)->count();
            $newRequestsCount = Requests::where('client_id', Auth::user()->id)->where('status', 'جديد')->count();
            $underProcessRequestsCount = Requests::where('client_id', Auth::user()->id)->where('status', 'تحت الاجراء')->count();
            $pendingRequestsCount = Requests::where('client_id', Auth::user()->id)->where('status', 'معلقة')->count();
            $closedRequestsCount = Requests::where('client_id', Auth::user()->id)->where('status', 'منتهية')->count();
        }

        if ($user->role == 'admin') {
            $systemIds = PartnerSystem::where('partner_id', $user->id)
                ->pluck('system_id');
            $requests = Requests::whereIn('system_id', $systemIds)
                ->with('user', 'system')
                ->when($statusFilter, function ($query) use ($statusFilter): void {
                    $query->where('status', $statusFilter);
                })
                ->when($search, function ($query) use ($search) {
                    $query->whereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
                })
                ->latest()
                ->paginate(8);
            $specialRequests = null;
        } elseif ($user->role == 'client') {
            $specialRequests = null;

            $requests = Requests::where('client_id', $user->id)
                ->with('user', 'system')
                ->when($statusFilter, function ($query) use ($statusFilter): void {
                    $query->where('status', $statusFilter);
                })
                ->when($search, function ($query) use ($search) {
                    $query->whereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
                })
                ->latest()
                ->paginate(8);
        } elseif ($user->role == 'partner') {
            $systemIds = PartnerSystem::where('partner_id', $user->id)
                ->pluck('system_id');
            $requests = Requests::whereIn('system_id', $systemIds)
                ->with('user', 'system')
                ->when($statusFilter, function ($query) use ($statusFilter): void {
                    $query->where('status', $statusFilter);
                })
                ->when($search, function ($query) use ($search) {
                    $query->whereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
                })
                ->latest()
                ->paginate(8);
        }
        $specialRequestss = SpecialRequest::query()
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

        return view('dashboard.requests.index', compact('specialRequestss','specialRequests', 'requests', 'allRequestsCount', 'newRequestsCount', 'underProcessRequestsCount', 'pendingRequestsCount', 'closedRequestsCount'));
    }

    // Create Method
    public function create()
    {
        $systems = System::all();
        $partners = User::where('role', 'partner')->get();
        $clients = User::where('role', 'client')->get();
        return view('dashboard.requests.create', compact('systems', 'partners', 'clients'));
    }

    // Store Method
    public function store(Request $request)
    {
        $request->validate([
            'order_number' => 'required',
            'system_id' => 'required|exists:systems,id',
            'client_id' => 'required|exists:users,id',
            'status' => 'required',
        ]);

        $request = Requests::create($request->all());

        return redirect()->route('dashboard.requests.index')->with('success', 'تم حفظ الطلب بنجاح');
    }

    // ClientStore Method
    public function clientStore(Request $request)
    {
        $validated = $request->validate([
            'system_id' => 'required|exists:systems,id',
            'client_id' => 'required|exists:users,id',
            'status' => 'required',
        ]);

        $validated['order_number'] = 'REQ' . time() . rand(1, 9);

        Requests::create($validated);

        return redirect()->route('system.show', ['system' => $request->system_id])
            ->with('success', '🎉 تم الاشتراك في النظام بنجاح! سيتم مراجعة طلبك قريباً');
    }

    // app/Http/Controllers/Dashboard/RequestsController.php

    public function show($id)
    {
        // جلب الطلب مع علاقة الشركاء (Partners)
        $SpecialRequest = Requests::with(['user', 'system', 'client', 'partners'])->findOrFail($id);

        // 1. جلب الـ IDs للشركاء المعينين حالياً للطلب لمنع تكرارهم في قائمة الاختيار
        $assignedPartnerIds = $SpecialRequest->partners->pluck('id')->toArray();

        // 2. جلب قائمة الشركاء المتاحين للاختيار (الذين لم يتم تعيينهم بعد)
        $partners = User::where('role', 'partner')
            ->whereNotIn('id', $assignedPartnerIds)
            ->get();

        // 3. جلب المدراء (Partners who are employees)
        $managers = User::where('role', 'partner')
            ->where('is_employee', 1)
            ->get();

        // 4. جلب بيانات الدعم (Support) كما هي في كودك
        $collection1 = Support::where('request_id', $SpecialRequest->id)
            ->with(['user', 'unreadMessages', 'messages'])
            ->get()
            ->map(function ($item) {
                $item->is_technical = false;
                return $item;
            });

        $collection2 = \DB::table('technical_support')
            ->where('request_id', $SpecialRequest->id)
            ->get()
            ->map(function ($item) {
                $item->is_technical = true;
                return $item;
            });

        $allSupports = $collection1->concat($collection2)->sortByDesc('created_at');

        return view('dashboard.requests.show', [
            'SpecialRequest' => $SpecialRequest,
            'supports'       => $allSupports,
            'partners'       => $partners,
            'managers'       => $managers,
        ]);
    }

    // edit Method
    public function edit($id)
    {
        $userRequest = Requests::with('user', 'system')->findOrFail($id);
        $systems = System::all();
        $partners = User::where('role', 'partner')->get();
        $clients = User::where('role', 'client')->get();

        return view('dashboard.requests.edit', compact('userRequest', 'systems', 'partners', 'clients'));
    }

    // Update Method
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'order_number' => 'required',
            'system_id' => 'required|exists:systems,id',
            'client_id' => 'required|exists:users,id',
            'status' => 'required',
        ]);

        $requestModel = Requests::findOrFail($id);
        $requestModel->update($validated);

        return redirect()->route('dashboard.requests.index')
            ->with('success', 'تم تعديل الطلب بنجاح');
    }

    // UpdateStatus Method
    public function updateStatus(Request $request, Requests $userRequest)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $oldStatus = $userRequest->status;
        $newStatus = $request->status;

        $userRequest->update([
            'status' => $newStatus
        ]);
        if ($newStatus === 'منتهية' && $oldStatus !== 'منتهية') {
            $system = $userRequest->system;
            if (!$system) {
                goto redirect;
            }
            $startDate = $userRequest->created_at;
            $endDate = now();
            $actualDays = $startDate->diffInDays($endDate) + 1;
            $expectedDaysFrom = $system->execution_days_from;
            $expectedDaysTo = $system->execution_days_to;
            $targetDays = ($expectedDaysFrom + $expectedDaysTo) / 2;
            $executionTimeValue = $actualDays;
            $partnerSystem = PartnerSystem::where('system_id', $system->id)
                ->first();
            if ($partnerSystem) {
                $partnerId = $partnerSystem->partner_id;

                $performance = Performance::where('user_id', $partnerId)
                    ->where('performance_date', now()->toDateString())
                    ->first();

                if ($performance) {
                    $performance->increment('completed_tasks');
                    $performance->execution_time = min(100, max(0, $performance->execution_time) + $executionTimeValue);
                    $performance->save();
                } else {
                    Performance::create([
                        'user_id' => $partnerId,
                        'performance_date' => now()->toDateString(),
                        'execution_time' => $executionTimeValue,
                        'completed_tasks' => 1,
                    ]);
                }
            }
        }

        redirect:
        if ($newStatus === 'منتهية' && !$userRequest->rating) {
            return redirect()->route('dashboard.requests.show', $userRequest->id)
                ->with('show_rating_modal', $userRequest->id)
                ->with('success', 'تم تسليم النظام بنجاح.');
        }

        return redirect()->route('dashboard.requests.show', $userRequest->id)
            ->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }

    // Invoice Method
    public function invoice($id)
    {
        $userRequest = Requests::with(['system.partners', 'client'])
            ->findOrFail($id);

        return view('dashboard.requests.invoice', compact('userRequest'));
    }

    // Special Invoice Method
    public function specialInvoice($id)
    {
        $userRequest = SpecialRequest::with(['system.partners'])
            ->findOrFail($id);

        return view('dashboard.requests.special-invoice', compact('userRequest'));
    }
    

    // Destroy Method
    public function destroy($id)
    {
        Requests::findOrFail($id)->delete();

        return redirect()->route('dashboard.requests.index')->with('success', 'تم حذف الطلب بنجاح');
    }

    // في RequestsController.php
    public function deliver($id)
    {
        // 1. جلب الطلب العادي باستخدام الموديل الخاص به
        $request = Requests::findOrFail($id);

        // 2. التحقق من الصلاحية (الأدمن فقط)
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'عذراً، لا تمتلك الصلاحية لتنفيذ هذا الإجراء.');
        }

        // 3. تحديث الحالة
        // ملاحظة: تأكد أن حالة 'in_review' موجودة في الـ Status Labels التي عرفناها سابقاً في الموديل
        $request->status = 'waiting_client'; // أو 'in_review' حسب منطق العمل عندك
        $request->save();

        return redirect()->back()->with('success', 'تم إرسال الطلب للمراجعة من قبل العميل بنجاح.');
    }

    // إضافة مرحلة
    public function addStage(Request $request, $requestId)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'hours_count' => 'required|numeric|min:0',
            'end_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $userRequest = Requests::findOrFail($requestId);
        $userRequest->stages()->create($data);

        return back()->with('success', 'تمت إضافة المرحلة بنجاح');
    }

    // تحديث مرحلة
    public function updateStage(Request $request, $stageId)
    {
        $stage = RequestStage::findOrFail($stageId);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'hours_count' => 'required|numeric|min:0',
            'end_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $stage->update($data);
        return back()->with('success', 'تم تحديث المرحلة بنجاح');
    }

    // حذف مرحلة
    public function destroyStage($id)
    {
        RequestStage::findOrFail($id)->delete();
        return back()->with('success', 'تم حذف المرحلة بنجاح');
    }

    public function addNote(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'visible_to_client' => 'nullable',
        ]);

        $userRequest = Requests::findOrFail($id);

        $userRequest->notes()->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'user_id' => auth()->id(),
            'visible_to_client' => $request->has('visible_to_client'),
        ]);

        return back()->with('success', 'تمت إضافة الملاحظة بنجاح');
    }

    public function destroyNote($id)
    {
        $note = RequestNote::findOrFail($id);
        if (auth()->user()->role !== 'admin' && auth()->id() !== $note->user_id) {
            abort(403);
        }
        $note->delete();
        return back()->with('success', 'تم حذف الملاحظة بنجاح');
    }

    public function updateProjectBudget(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'payment_type' => 'required|in:single,installments',
            'installments' => 'nullable|array',
        ]);

        $userRequest = Requests::findOrFail($id);

        DB::transaction(function () use ($request, $userRequest) {
            // تحديث السعر الأساسي في جدول الطلبات العادية
            $userRequest->update([
                'price' => $request->price,
                'payment_type' => $request->payment_type,
            ]);

            // حذف الدفعات القديمة المرتبطة بهذا الطلب فقط
            RequestPayment::where('request_id', $userRequest->id)->delete();

            if ($request->payment_type == 'single') {
                RequestPayment::create([
                    'request_id' => $userRequest->id,
                    'payment_name' => 'الدفعة الكاملة',
                    'amount' => $request->price,
                    'status' => 'unpaid'
                ]);
            } else {
                foreach ($request->installments as $installment) {
                    RequestPayment::create([
                        'request_id' => $userRequest->id,
                        'payment_name' => $installment['name'],
                        'amount' => $installment['amount'],
                        'due_date' => $installment['due_date'] ?? null,
                        'status' => 'unpaid'
                    ]);
                }
            }
        });

        return back()->with('success', 'تم تحديث ميزانية الطلب بنجاح');
    }
}
