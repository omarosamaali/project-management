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
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();
        $statusFilter = $request->get('status');
        if ($user->role == 'admin') {
            $baseRequests = Requests::query();
            $baseSpecialRequests = SpecialRequest::query();
            $basePartnerSpecialRequests = SpecialRequestPartner::query();
        } elseif ($user->role == 'partner') {
            $systemIds = PartnerSystem::where('partner_id', $user->id)->pluck('system_id');
            $baseRequests = Requests::whereIn('system_id', $systemIds);
            $assignedSpecialIds = SpecialRequestPartner::where('partner_id', $user->id)
                ->whereNotNull('special_request_id')
                ->pluck('special_request_id');
            $baseSpecialRequests = SpecialRequest::whereIn('id', $assignedSpecialIds);
            $basePartnerSpecialRequests = SpecialRequestPartner::where('partner_id', $user->id);
        } else {
            $baseRequests = Requests::where('client_id', $user->id);
            $baseSpecialRequests = SpecialRequest::where('user_id', $user->id);
            $basePartnerSpecialRequests = SpecialRequestPartner::whereRaw('1 = 0');
        }
        $allRequestsCount = (clone $baseRequests)->count() + (clone $baseSpecialRequests)->count();
        $newRequestsCount = (clone $baseRequests)->where('status', 'جديد')->count() +
            (clone $baseSpecialRequests)->where('status', 'جديد')->count();
        $underProcessRequestsCount = (clone $baseRequests)->where('status', 'تحت الاجراء')->count() +
            (clone $baseSpecialRequests)->where('status', 'تحت الاجراء')->count();
        $pendingRequestsCount = (clone $baseRequests)->where('status', 'معلقة')->count() +
            (clone $baseSpecialRequests)->where('status', 'معلقة')->count();
        $closedRequestsCount = (clone $baseRequests)->where('status', 'منتهية')->count() +
            (clone $baseSpecialRequests)->where('status', 'منتهية')->count();
        $requests = $baseRequests->with(['system', 'client'])
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
            ->when($search, fn($q) => $q->whereHas('client', fn($sq) => $sq->where('name', 'like', "%$search%")))
            ->latest()
            ->paginate(8, ['*'], 'requests_page');
        $specialRequestss = $baseSpecialRequests->with(['user'])
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhereHas('user', fn($sq) => $sq->where('name', 'like', "%$search%"));
            })->latest()->paginate(8, ['*'], 'special_page');

        $specialRequests = $basePartnerSpecialRequests->with(['specialRequest.user', 'partner'])
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
            ->latest()
            ->paginate(8, ['*'], 'partner_special_page');

        return view('dashboard.requests.index', compact(
            'requests',
            'specialRequestss',
            'specialRequests',
            'allRequestsCount',
            'newRequestsCount',
            'underProcessRequestsCount',
            'pendingRequestsCount',
            'closedRequestsCount'
        ));
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
        $SpecialRequest = Requests::with([
            'user',
            'system',
            'client',
            'partners',
            'requestFiles.user',
            'projectMeetings.participants',
            'messages.user'
        ])->findOrFail($id);

        $assignedPartnerIds = $SpecialRequest->partners->pluck('id')->toArray();
        $partners = User::where('role', 'partner')
            ->whereNotIn('id', $assignedPartnerIds)
            ->get();

        $managers = User::where('role', 'partner')
            ->where('is_employee', 1)
            ->get();

        // استخدم نظام الرسائل الجديد
        $supports = $SpecialRequest->messages()->with('user')->oldest()->get();

        return view('dashboard.requests.show', [
            'SpecialRequest' => $SpecialRequest,
            'supports'       => $supports,
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
            // تحديث السعر الأساسي في جدول المشاريع العادية
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
