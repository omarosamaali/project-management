<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\TechnicalSupport;
use App\Models\Requests;
use App\Models\SpecialRequest;
use App\Services\WhatsAppOTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Performance;
use Illuminate\Support\Collection;

class TechnicalSupportController extends Controller
{
    public function index()
    {
        $query = TechnicalSupport::with(['request.system', 'specialRequest', 'client']);

        if (Auth::user()->role !== 'admin') {
            $query->where('client_id', Auth::id());
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        $activeRequests = collect();
        if (Auth::user()->role !== 'admin') {
            $activeRequests = $this->mergedActiveSupportProjects(Auth::id());
        }

        return view('dashboard.technical_support.index', compact('tickets', 'activeRequests'));
    }

    public function create()
    {
        $userId = Auth::id();

        $supportProjects = $this->mergedActiveSupportProjects($userId);
        $userRequests    = $supportProjects;

        return view('dashboard.technical_support.create', compact('userRequests', 'supportProjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject'      => 'required|string|max:255',
            'description'  => 'required|string',
            'project_key'  => ['required', 'string', 'regex:/^(request|special):\d+$/'],
        ]);

        [$type, $id] = explode(':', $request->project_key, 2);
        $userId      = Auth::id();

        if ($type === 'request') {
            $requestModel = Requests::with('system')->find($id);

            if (!$requestModel || !$this->clientOwnsRequest($requestModel, $userId)) {
                return redirect()->back()->withInput()
                    ->withErrors(['project_key' => 'المشروع غير موجود أو لا يخصك.']);
            }

            if (!$requestModel->has_active_support) {
                return redirect()->back()->withInput()
                    ->withErrors(['project_key' => 'انتهت مدة الدعم الفني / الصيانة لهذا المشروع.']);
            }

            $ticket = TechnicalSupport::create([
                'client_id'   => $userId,
                'request_id'  => $requestModel->id,
                'system_id'   => $requestModel->system_id,
                'subject'     => $request->subject,
                'description' => $request->description,
                'status'      => 'open',
            ]);

            $this->notifyPartnersForRequest($ticket, $requestModel);
        } else {
            $special = SpecialRequest::find($id);

            if (!$special || !$this->clientOwnsSpecial($special, $userId)) {
                return redirect()->back()->withInput()
                    ->withErrors(['project_key' => 'المشروع غير موجود أو لا يخصك.']);
            }

            if (!$special->has_active_support) {
                return redirect()->back()->withInput()
                    ->withErrors(['project_key' => 'انتهت فترة الصيانة لهذا المشروع.']);
            }

            $ticket = TechnicalSupport::create([
                'client_id'          => $userId,
                'special_request_id' => $special->id,
                'subject'            => $request->subject,
                'description'        => $request->description,
                'status'             => 'open',
            ]);

            $this->notifyPartnersForSpecial($ticket, $special);
        }

        return redirect()->route('dashboard.technical_support.index')
            ->with('success', 'تم إنشاء التذكرة بنجاح وتم إشعار الفريق المختص');
    }

    public function show(TechnicalSupport $technicalSupport)
    {
        $technicalSupport->load(['request.system', 'specialRequest', 'client']);

        return view('dashboard.technical_support.show', [
            'ticket' => $technicalSupport,
        ]);
    }

    public function update(Request $request, TechnicalSupport $technicalSupport)
    {
        $oldStatus = $technicalSupport->status;
        $newStatus = $request->status;
        $technicalSupport->update(['status' => $newStatus]);

        try {
            $whatsapp    = app(WhatsAppOTPService::class);
            $projectName = $technicalSupport->project_name;
            $whatsapp->notifyManager(
                "تم تحديث تذكرة الدعم الفني: ({$technicalSupport->subject}) — الحالة: {$oldStatus} ← {$newStatus}",
                $projectName
            );
        } catch (\Exception $e) {
            Log::error("[TICKET_UPDATE] فشل إشعار المدير: " . $e->getMessage());
        }

        if (in_array($newStatus, ['resolved', 'closed'])) {
            $partner = null;

            if ($technicalSupport->request_id && $technicalSupport->request?->system) {
                $partner = $technicalSupport->request->system->partners()->first();
            } elseif ($technicalSupport->special_request_id) {
                $partner = $technicalSupport->specialRequest?->partners()->first();
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

        if ($technicalSupport->special_request_id) {
            return redirect()
                ->route('dashboard.special-request.show', $technicalSupport->special_request_id)
                ->with('success', 'تم تحديث حالة التذكرة');
        }

        if ($technicalSupport->request_id) {
            return redirect()
                ->route('dashboard.requests.show', $technicalSupport->request_id)
                ->with('success', 'تم تحديث حالة التذكرة');
        }

        return redirect()->back()->with('success', 'تم تحديث الحالة بنجاح');
    }

    private function mergedActiveSupportProjects(int $userId): Collection
    {
        return $this->activeGeneralRequestsForUser($userId)
            ->concat($this->activeSpecialRequestsForUser($userId));
    }

    private function activeGeneralRequestsForUser(int $userId): Collection
    {
        return Requests::forClient($userId)
            ->where('status', 'closed')
            ->whereNotNull('delivered_at')
            ->with('system')
            ->get()
            ->filter(fn ($r) => $r->has_active_support)
            ->values();
    }

    private function activeSpecialRequestsForUser(int $userId): Collection
    {
        return SpecialRequest::forClient($userId)
            ->where('status', 'completed')
            ->whereNotNull('delivered_at')
            ->get()
            ->filter(fn ($s) => $s->has_active_support)
            ->values();
    }

    private function clientOwnsRequest(Requests $model, int $userId): bool
    {
        return $model->isClientMember($userId);
    }

    private function clientOwnsSpecial(SpecialRequest $model, int $userId): bool
    {
        return $model->isClientMember($userId);
    }

    private function notifyPartnersForRequest(TechnicalSupport $ticket, Requests $requestModel): void
    {
        $projectName = $requestModel->system->name_ar ?? ('مشروع #' . $requestModel->id);
        $this->sendTicketWhatsApp($ticket, $projectName, function () use ($ticket) {
            return DB::table('special_request_partner')
                ->where('special_request_partner.request_id', $ticket->request_id)
                ->join('users', 'users.id', '=', 'special_request_partner.partner_id')
                ->where('users.role', 'partner')
                ->whereNotNull('users.phone')
                ->select('users.id', 'users.name', 'users.phone')
                ->get();
        });
    }

    private function notifyPartnersForSpecial(TechnicalSupport $ticket, SpecialRequest $special): void
    {
        $projectName = $special->title ?? ('مشروع خاص #' . $special->id);
        $this->sendTicketWhatsApp($ticket, $projectName, function () use ($special) {
            return DB::table('special_request_partner')
                ->where('special_request_partner.special_request_id', $special->id)
                ->join('users', 'users.id', '=', 'special_request_partner.partner_id')
                ->where('users.role', 'partner')
                ->whereNotNull('users.phone')
                ->select('users.id', 'users.name', 'users.phone')
                ->get();
        });
    }

    private function sendTicketWhatsApp(TechnicalSupport $ticket, string $projectName, callable $partnersQuery): void
    {
        try {
            $whatsapp = app(WhatsAppOTPService::class);

            foreach ([
                WhatsAppOTPService::MANAGER_PHONE => 'المدير',
                WhatsAppOTPService::ADMIN_PHONE   => 'الأدمن',
            ] as $fixedPhone => $fixedName) {
                $whatsapp->sendTicketNotification(
                    phone: $fixedPhone,
                    partnerName: $fixedName,
                    projectName: $projectName,
                    ticketId: $ticket->id
                );
            }

            $partners = $partnersQuery();

            foreach ($partners as $partner) {
                $whatsapp->sendTicketNotification(
                    phone: $partner->phone,
                    partnerName: $partner->name,
                    projectName: $projectName,
                    ticketId: $ticket->id
                );
            }
        } catch (\Exception $e) {
            Log::error("[TICKET] فشل إرسال الإشعارات", [
                'ticket_id' => $ticket->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }
}
