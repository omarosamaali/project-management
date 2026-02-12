<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\TechnicalSupport;
use App\Models\Requests;
use App\Models\SpecialRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Performance;
use App\Models\WhatsAppMessage;
use Carbon\Carbon;

class TechnicalSupportController extends Controller
{
    // Index Method
    public function index()
    {
        $query = WhatsAppMessage::with('user');

        if (Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('dashboard.technical_support.index', compact('messages'));
    }

    // Create Method
    public function create()
    {
        $userId = Auth::id();

        // 1. جلب سجلات جدول الـ Requests (تأكد من اسم العمود هنا أيضاً)
        $generalRequests = Requests::where('client_id', $userId) // أو user_id حسب جدولك
            ->whereHas('system', function ($query) {
                $query->where('support_days', '>', 0)
                    ->whereRaw('support_days >= DATEDIFF(CURDATE(), requests.created_at)');
            })
            ->with('system')
            ->get();
        $specialRequests = SpecialRequest::where('user_id', $userId)
            ->get();

        // 3. دمج المجموعتين
        $allRequests = $generalRequests->concat($specialRequests);

        return view('dashboard.technical_support.create', [
            'userRequests' => $allRequests,
        ]);
    }
    // Store Method
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'request_id' => 'required|exists:requests,id',
        ]);

        $requestModel = null;
        if ($request->filled('request_id')) {
            $requestModel = Requests::find($request->request_id);
        }

        TechnicalSupport::create([
            'client_id' => Auth::id(),
            'request_id' => $request->request_id,
            'system_id' => $requestModel->system_id ?? null, // جلب ID النظام المرتبط بالطلب إن وجد
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'open',
        ]);

        return redirect()->back()->with('success', 'تم انشاء التذكرة بنجاح');
    }

    // Show Method
    public function show(TechnicalSupport $technicalSupport)
    {
        $technicalSupport->load(['request', 'client']);
        return view('dashboard.technical_support.show', [
            'ticket' => $technicalSupport,
        ]);
    }

    // Update Method
    public function update(Request $request, TechnicalSupport $technicalSupport)
    {
        $newStatus = $request->status;

        // 1. تحديث الحالة
        $technicalSupport->update(['status' => $newStatus]);

        // 2. منطق تسجيل الأداء (Performance)
        if (in_array($newStatus, ['resolved', 'closed'])) {
            $partner = null;

            if ($technicalSupport->request && $technicalSupport->request->system) {
                $partner = $technicalSupport->request->system
                    ->partners()
                    ->first();
            }

            if ($partner) {
                Performance::create([
                    'user_id'                => $partner->id,
                    'response_speed'         => $technicalSupport->created_at->diffInMinutes(now()),
                    'execution_time'         => 0,
                    'message_response_rate'  => 0,
                    'support_tickets_closed' => 1,
                    'completed_tasks'        => 1,
                    'performance_date'       => today(),
                ]);
            } else {
                Log::warning("Performance not recorded: No partner found for ticket ID {$technicalSupport->id}");
            }
        }

        // 3. الحل: الرجوع لصفحة الطلب الخاص (Special Request)
        // نتحقق إذا كانت التذكرة مرتبطة بطلب خاص
        if ($technicalSupport->request_id) {
            return redirect()->route('dashboard.special-request.show', $technicalSupport->request_id)
                ->with('success', 'تم تحديث حالة التذكرة والعودة للمشروع');
        }

        // لو مفيش request_id (حالة احتياطية) يرجع مكانه
        return redirect()->back()->with('success', 'تم تحديث الحالة بنجاح');
    }
}
