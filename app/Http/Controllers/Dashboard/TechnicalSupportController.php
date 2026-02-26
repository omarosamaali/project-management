<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\TechnicalSupport;
use App\Models\Requests;
use App\Models\SpecialRequest;
use App\Services\WhatsAppOTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Performance;

class TechnicalSupportController extends Controller
{
    // ── Index ──────────────────────────────────────────
    public function index()
    {
        $query = TechnicalSupport::with(['request.system', 'client']);

        if (Auth::user()->role !== 'admin') {
            $query->where('client_id', Auth::id());
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        // بانر الدعم الفني — للعميل فقط
        $activeRequests = collect();
        if (Auth::user()->role !== 'admin') {
            $activeRequests = Requests::where('client_id', Auth::id())
                ->where('status', 'closed')
                ->whereHas('system', fn($q) => $q->where('support_days', '>', 0))
                ->with('system')
                ->get()
                ->filter(fn($r) => $r->has_active_support);
        }

        return view('dashboard.technical_support.index', compact('tickets', 'activeRequests'));
    }

    // ── Create ─────────────────────────────────────────
    public function create()
    {
        $userId = Auth::id();

        $generalRequests = Requests::where('client_id', $userId)
            ->where('status', 'closed')
            ->whereHas('system', fn($q) => $q->where('support_days', '>', 0))
            ->with('system')
            ->get()
            ->filter(fn($r) => $r->has_active_support);

        $specialRequests = SpecialRequest::where('user_id', $userId)->get();
        $allRequests     = $generalRequests->concat($specialRequests);

        return view('dashboard.technical_support.create', [
            'userRequests' => $allRequests,
        ]);
    }

    // ── Store ──────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string',
            'request_id'  => 'required|exists:requests,id',
        ]);

        $requestModel = Requests::with('system')->find($request->request_id);

        if ($requestModel && !$requestModel->has_active_support) {
            return redirect()->back()->withInput()
                ->withErrors(['request_id' => 'انتهت مدة الدعم الفني لهذا المشروع.']);
        }

        // ── إنشاء التذكرة ──
        $ticket = TechnicalSupport::create([
            'client_id'   => Auth::id(),
            'request_id'  => $request->request_id,
            'system_id'   => $requestModel->system_id ?? null,
            'subject'     => $request->subject,
            'description' => $request->description,
            'status'      => 'open',
        ]);

        // ── إرسال واتساب للـ partners المرتبطين بهذا الطلب ──
        $this->notifyPartners($ticket, $requestModel);

        return redirect()->route('dashboard.technical_support.index')
            ->with('success', 'تم إنشاء التذكرة بنجاح وتم إشعار الفريق المختص');
    }

    // ── Show ───────────────────────────────────────────
    public function show(TechnicalSupport $technicalSupport)
    {
        $technicalSupport->load(['request.system', 'client']);
        return view('dashboard.technical_support.show', [
            'ticket' => $technicalSupport,
        ]);
    }

    // ── Update ─────────────────────────────────────────
    public function update(Request $request, TechnicalSupport $technicalSupport)
    {
        $newStatus = $request->status;
        $technicalSupport->update(['status' => $newStatus]);

        if (in_array($newStatus, ['resolved', 'closed'])) {
            $partner = null;
            if ($technicalSupport->request?->system) {
                $partner = $technicalSupport->request->system->partners()->first();
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
                Log::warning("Performance not recorded: No partner for ticket #{$technicalSupport->id}");
            }
        }

        if ($technicalSupport->request_id) {
            return redirect()
                ->route('dashboard.special-request.show', $technicalSupport->request_id)
                ->with('success', 'تم تحديث حالة التذكرة');
        }

        return redirect()->back()->with('success', 'تم تحديث الحالة بنجاح');
    }

    // ── إرسال إشعار واتساب عند فتح تذكرة ──────────
    // يبعت للرقم الثابت + كل الـ partners المرتبطين بالطلب
    private function notifyPartners(TechnicalSupport $ticket, Requests $requestModel): void
    {
        try {
            $whatsapp    = app(WhatsAppOTPService::class);
            $projectName = $requestModel->system->name_ar ?? ('مشروع #' . $requestModel->id);

            // ── 1. الرقم الثابت دايماً ──
            $fixedPhone = '+971501774477';
            Log::info("[TICKET] إرسال للرقم الثابت", ['phone' => $fixedPhone, 'ticket_id' => $ticket->id]);
            $sent = $whatsapp->sendTicketNotification(
                phone: $fixedPhone,
                partnerName: 'فريق الدعم',
                projectName: $projectName,
                ticketId: $ticket->id
            );
            Log::info("[TICKET] الرقم الثابت: " . ($sent ? 'نجح ✓' : 'فشل ✗'));

            // ── 2. الـ partners المرتبطين بـ request_id ──
            $partners = \Illuminate\Support\Facades\DB::table('special_request_partner')
                ->where('special_request_partner.request_id', $ticket->request_id)
                ->join('users', 'users.id', '=', 'special_request_partner.partner_id')
                ->where('users.role', 'partner')
                ->whereNotNull('users.phone')
                ->select('users.id', 'users.name', 'users.phone')
                ->get();

            if ($partners->isEmpty()) {
                Log::info("[TICKET] لا يوجد partners للطلب #{$ticket->request_id}");
                return;
            }

            foreach ($partners as $partner) {
                Log::info("[TICKET] إرسال للـ partner", [
                    'partner_id' => $partner->id,
                    'phone'      => $partner->phone,
                    'ticket_id'  => $ticket->id,
                ]);
                $sent = $whatsapp->sendTicketNotification(
                    phone: $partner->phone,
                    partnerName: $partner->name,
                    projectName: $projectName,
                    ticketId: $ticket->id
                );
                Log::info("[TICKET] partner #{$partner->id}: " . ($sent ? 'نجح ✓' : 'فشل ✗'));
            }
        } catch (\Exception $e) {
            Log::error("[TICKET] فشل إرسال الإشعارات", [
                'ticket_id' => $ticket->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }
}
