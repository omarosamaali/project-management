<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\SpecialRequest;
use App\Models\RequestStage;
use Illuminate\Http\Request;
use App\Models\RequestBudget;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsAppOTPService;

class SpecialRequestController extends Controller
{
    public function markPaymentAsPaid(\App\Models\RequestPayment $payment)
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        try {
            $project = $payment->specialRequest;
            $whatsapp = app(WhatsAppOTPService::class);
            $whatsapp->notifyManager(
                "تم تأكيد دفعة: ({$payment->payment_name}) بمبلغ {$payment->amount}",
                $project->title ?? "طلب #{$payment->special_request_id}"
            );
        } catch (\Exception $e) {
            \Log::error("[PAYMENT] فشل إشعار المدير: " . $e->getMessage());
        }

        return back()->with('success', 'تم تحويل الدفعة إلى مدفوعة بنجاح ✅');
    }
    
    public function updateBudgetStages(Request $request, $id)
    {
        \Log::info('updateBudgetStages reached', [
            'id' => $id,
            'user' => auth()->id() ?? 'guest',
            'input' => $request->all()
        ]);
        $specialRequest = SpecialRequest::findOrFail($id);
        $request->validate([
            'total_price' => 'required|numeric|min:0',
            'stages' => 'required|array|min:1',
            'stages.*.name' => 'required|string',
            'stages.*.amount' => 'required|numeric|min:0',
            'stages.*.date' => 'nullable|date',
        ]);
        \DB::transaction(function () use ($request, $specialRequest) {
            $specialRequest->update(['price' => $request->total_price]);
            RequestBudget::where('parent_request_id', $specialRequest->id)->delete();
            foreach ($request->stages as $stage) {
                RequestBudget::create([
                    'parent_request_id' => $specialRequest->id,
                    'stage_name'        => $stage['name'],
                    'stage_amount'      => $stage['amount'],
                    'expected_date'     => $stage['date'],
                    'payment_status'    => 'unpaid',
                ]);
            }
        });
        return back()->with('success', 'تم تحديث الميزانية بنجاح');
    }

    // Index Method
    public function index()
    {
        $services = Service::where('status', 'active')->get();
        return view('special-request.index', compact('services'));
    }

    // Store Method
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'project_type' => ['required'],
            'description' => ['required', 'string'],
            'core_features' => ['required', 'string'],
            'examples' => ['nullable', 'string', 'url'],
            'budget' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
        ]);

        $specialRequest = SpecialRequest::create([
            'order_number' => 'REQ' . time() . rand(1, 9),
            'user_id' => Auth::id(),
            'title' => $request->title,
            'project_type' => $request->project_type,
            'description' => $request->description,
            'core_features' => $request->core_features,
            'examples' => $request->examples,
            'budget' => $request->budget,
            'deadline' => $request->deadline,
            'is_project' => false,
            'status' => 'pending',
        ]);

        $whatsapp = app(WhatsAppOTPService::class);
        $client = Auth::user();

        // إشعار المدير والأدمن بالطلب الجديد
        try {
            $eventText = "طلب خاص جديد من: {$client->name} — نوع الطلب: {$request->project_type}";
            $whatsapp->notifyManager($eventText, $request->title);
        } catch (\Exception $e) {
            \Log::error("[SPECIAL_REQUEST] فشل إشعار المدير: " . $e->getMessage());
        }

        // إشعار العميل نفسه بتأكيد استلام طلبه
        try {
            if ($client->phone) {
                $whatsapp->sendProjectNotification(
                    $client->phone,
                    $client->name,
                    "تم استلام طلبك الخاص بنجاح ✅ سنراجعه ونتواصل معك قريباً",
                    $request->title
                );
            }
        } catch (\Exception $e) {
            \Log::error("[SPECIAL_REQUEST] فشل إشعار العميل: " . $e->getMessage());
        }

        return redirect()->route('special-request.index')->with('success', '✅ تم إنشاء طلبك بنجاح');
    }

    // Show Method
    public function show() // بدون parameter
    {
        // جلب كل المشاريع مع علاقاتها
        $specialRequests = SpecialRequest::with(['proposals.user', 'partners', 'projectManager'])
            ->where('user_id', Auth::id())->paginate(8); // أو ->paginate(10) لو عايز pagination

        // البيانات الإضافية
        $partners = User::where('role', 'partner')->get();
        $managers = User::where('role', 'manager')->get();

        return view('special-request.show', compact('specialRequests', 'partners', 'managers'));
    }

    // Show Method
    public function showSpecialRequest(SpecialRequest $specialRequest)
    {
        if (Auth::id() !== $specialRequest->user_id) {
            abort(403, 'غير مصرح لك بمشاهدة تفاصيل هذا الطلب.');
        }
        return view('special-request.show-special-request', compact('specialRequest'));
    }

    // Edit Method
    public function edit(SpecialRequest $specialRequest)
    {
        if (Auth::id() !== $specialRequest->user_id) {
            abort(403, 'غير مصرح لك بتعديل هذا الطلب.');
        }

        return view('special-request.edit', compact('specialRequest'));
    }

    // Update Method
    public function update(Request $request, SpecialRequest $specialRequest)
    {
        if (Auth::id() !== $specialRequest->user_id) {
            abort(403, 'غير مصرح لك بتحديث هذا الطلب.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'project_type' => ['required', 'string'],
            'description' => ['required', 'string'],
            'core_features' => ['required', 'string'],
            'examples' => ['nullable', 'string', 'url'],
            'budget' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
        ]);

        $specialRequest->update([
            'title' => $request->title,
            'project_type' => $request->project_type,
            'description' => $request->description,
            'core_features' => $request->core_features,
            'examples' => $request->examples,
            'budget' => $request->budget,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('special-request.show')->with('success', 'تم تحديث الطلب بنجاح.');
    }

    // Destroy Method
    public function destroy(SpecialRequest $specialRequest)
    {
        $specialRequest->delete();
        return redirect()->route('special-request.show')->with('success', 'تم حذف الطلب بنجاح.');
    }


}
