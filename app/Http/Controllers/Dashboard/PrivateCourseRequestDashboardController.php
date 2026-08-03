<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PrivateCourseRefund;
use App\Models\PrivateCourseRequest;
use App\Models\User;
use App\Support\PrivateCourseRequestService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PrivateCourseRequestDashboardController extends Controller
{
    public function __construct(
        protected PrivateCourseRequestService $service,
    ) {}

    public function traineeIndex(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->canLearnCourses(), 403);

        $requests = PrivateCourseRequest::query()
            ->where('trainee_id', $user->id)
            ->with(['sourceCourse', 'trainer', 'privateCourse', 'refund'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $pendingRefunds = PrivateCourseRefund::query()
            ->where('trainee_id', $user->id)
            ->where('status', PrivateCourseRefund::STATUS_PENDING_TRAINEE_CONFIRM)
            ->whereNull('trainee_confirmed_at')
            ->with(['request.sourceCourse'])
            ->latest()
            ->get();

        return view('dashboard.academy.private-requests.trainee-index', compact('requests', 'pendingRefunds'));
    }

    public function trainerInbox(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isTrainer() || $user->isAdmin(), 403);

        $requests = PrivateCourseRequest::query()
            ->where('trainer_id', $user->id)
            ->with(['sourceCourse', 'trainee'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.academy.private-requests.trainer-inbox', compact('requests'));
    }

    public function adminUnassigned(Request $request)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $requests = PrivateCourseRequest::query()
            ->whereNull('trainer_id')
            ->with(['sourceCourse', 'trainee'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.academy.private-requests.admin-unassigned', compact('requests'));
    }

    public function adminIndex(Request $request)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $status = trim((string) $request->query('status', ''));

        $query = PrivateCourseRequest::query()
            ->with(['sourceCourse', 'trainee', 'trainer'])
            ->latest();

        if ($status !== '') {
            $query->where('status', $status);
        }

        $requests = $query->paginate(20)->withQueryString();

        $statusCounts = PrivateCourseRequest::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboard.academy.private-requests.admin-index', compact('requests', 'status', 'statusCounts'));
    }

    public function approve(Request $request, PrivateCourseRequest $privateRequest)
    {
        $this->authorizeTrainerAction($privateRequest);

        $data = $request->validate([
            'proposed_start_at' => 'required|date',
            'proposed_end_at' => 'required|date|after:proposed_start_at',
            'note' => 'nullable|string|max:2000',
        ]);

        $this->service->approveWithDates(
            $privateRequest,
            Auth::user(),
            Carbon::parse($data['proposed_start_at']),
            Carbon::parse($data['proposed_end_at']),
            $data['note'] ?? null,
        );

        return back()->with('success', __('messages.private_request_dates_proposed'));
    }

    public function reject(Request $request, PrivateCourseRequest $privateRequest)
    {
        $this->authorizeTrainerAction($privateRequest);

        $data = $request->validate([
            'rejection_reason' => 'required|string|max:2000',
        ]);

        $this->service->reject($privateRequest, Auth::user(), $data['rejection_reason']);

        return back()->with('success', __('messages.private_request_rejected_success'));
    }

    public function respondToDateChange(Request $request, PrivateCourseRequest $privateRequest)
    {
        $this->authorizeTrainerAction($privateRequest);
        abort_unless(
            $privateRequest->status === PrivateCourseRequest::STATUS_DATES_CHANGE_REQUESTED,
            422
        );

        $data = $request->validate([
            'proposed_start_at' => 'required|date',
            'proposed_end_at' => 'required|date|after:proposed_start_at',
            'note' => 'nullable|string|max:2000',
        ]);

        $this->service->approveWithDates(
            $privateRequest,
            Auth::user(),
            Carbon::parse($data['proposed_start_at']),
            Carbon::parse($data['proposed_end_at']),
            $data['note'] ?? null,
        );

        return back()->with('success', __('messages.private_request_dates_updated'));
    }

    public function block(Request $request, PrivateCourseRequest $privateRequest)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'block_reason' => 'required|string|max:2000',
        ]);

        $this->service->block($privateRequest, Auth::user(), $data['block_reason']);

        return back()->with('success', __('messages.private_request_blocked_success'));
    }

    public function confirmRefund(PrivateCourseRefund $refund)
    {
        abort_unless((int) $refund->trainee_id === (int) Auth::id(), 403);
        abort_unless(
            $refund->status === PrivateCourseRefund::STATUS_PENDING_TRAINEE_CONFIRM,
            422
        );

        $refund->update([
            'status' => PrivateCourseRefund::STATUS_REFUNDED,
            'trainee_confirmed_at' => now(),
        ]);

        if ($refund->request) {
            $this->service->addEvent($refund->request, Auth::user(), 'refund_confirmed', null, [
                'refund_id' => $refund->id,
            ]);
        }

        return back()->with('success', __('messages.private_refund_confirmed'));
    }

    public function refundsIndex(Request $request)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $status = trim((string) $request->query('status', ''));

        $query = PrivateCourseRefund::query()
            ->with(['trainee', 'request.sourceCourse', 'payment'])
            ->latest();

        if ($status !== '') {
            $query->where('status', $status);
        }

        $refunds = $query->paginate(20)->withQueryString();

        return view('dashboard.academy.refunds.index', compact('refunds', 'status'));
    }

    public function uploadRefundScreenshot(Request $request, PrivateCourseRefund $refund)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'screenshot' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'admin_note' => 'nullable|string|max:2000',
        ]);

        if ($refund->screenshot_path) {
            Storage::disk('public')->delete($refund->screenshot_path);
        }

        $path = $data['screenshot']->store('private-refunds', 'public');

        $refund->update([
            'status' => PrivateCourseRefund::STATUS_PENDING_TRAINEE_CONFIRM,
            'screenshot_path' => $path,
            'screenshot_uploaded_at' => now(),
            'admin_id' => Auth::id(),
            'admin_note' => $data['admin_note'] ?? null,
            'trainee_confirm_due_at' => now()->addDay(),
            'trainee_confirmed_at' => null,
        ]);

        if ($refund->request) {
            $this->service->addEvent($refund->request, Auth::user(), 'refund_screenshot_uploaded', null, [
                'refund_id' => $refund->id,
            ]);
        }

        return back()->with('success', __('messages.private_refund_screenshot_uploaded'));
    }

    protected function authorizeTrainerAction(PrivateCourseRequest $privateRequest): void
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        abort_unless($user->isTrainer(), 403);

        $isAssignedTrainer = $privateRequest->trainer_id
            && (int) $privateRequest->trainer_id === (int) $user->id;

        abort_unless($isAssignedTrainer, 403, __('messages.private_request_forbidden'));
    }
}
