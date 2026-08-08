<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PrivateCourseRefund;
use App\Models\PrivateCourseRefundScreenshot;
use App\Models\PrivateCourseRequest;
use App\Models\Setting;
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
            ->with(['request.sourceCourse', 'screenshots'])
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

    public function updateMeetingLink(Request $request, PrivateCourseRequest $privateRequest)
    {
        $user = Auth::user();
        abort_unless($user->isAdmin() || ((int) $privateRequest->trainer_id === (int) $user->id), 403);
        abort_unless($privateRequest->status === PrivateCourseRequest::STATUS_PAID, 422);
        abort_unless($privateRequest->private_course_id, 422);

        $privateRequest->loadMissing('privateCourse');
        $course = $privateRequest->privateCourse;
        abort_unless($course && $course->isPrivate() && ! $course->isCanceled(), 422);

        if (Setting::academyEmbeddedMeetingsEnabled()) {
            return back()->with('error', 'الاجتماعات المضمّنة مفعّلة — لا حاجة لإضافة رابط اجتماع خارجي.');
        }

        $data = $request->validate([
            'meeting_provider' => ['required', 'in:youtube,external'],
            'online_link' => [
                'required',
                'url',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                    if ($request->input('meeting_provider') === 'youtube'
                        && ! \App\Support\YouTubeLive::isYouTubeUrl((string) $value)) {
                        $fail(__('messages.private_meeting_link_youtube_invalid'));
                    }
                },
            ],
        ], [
            'meeting_provider.required' => __('messages.private_meeting_provider_required'),
            'online_link.required' => __('messages.private_meeting_link_required'),
            'online_link.url' => __('messages.private_meeting_link_invalid'),
        ]);

        $link = trim((string) $data['online_link']);
        if ($data['meeting_provider'] === 'youtube') {
            $watch = \App\Support\YouTubeLive::watchUrl($link);
            if ($watch) {
                $link = $watch;
            }
        }

        if ($course->start_date && now()->greaterThanOrEqualTo(Carbon::parse($course->start_date))) {
            return back()
                ->withInput()
                ->with('error', __('messages.private_meeting_link_deadline'));
        }

        $course->update(['online_link' => $link]);

        $this->service->addEvent($privateRequest, $user, 'meeting_link_saved', null, [
            'course_id' => $course->id,
            'meeting_provider' => $data['meeting_provider'],
        ]);

        return back()->with('success', __('messages.private_meeting_link_saved'));
    }

    public function confirmRefund(PrivateCourseRefund $refund)
    {
        abort_unless((int) $refund->trainee_id === (int) Auth::id(), 403);
        abort_unless(
            $refund->status === PrivateCourseRefund::STATUS_PENDING_TRAINEE_CONFIRM
            && $refund->trainee_confirmed_at === null
            && $refund->hasSuccessScreenshot(),
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
            ->with(['trainee', 'request.sourceCourse', 'payment', 'screenshots'])
            ->latest();

        if ($status !== '') {
            $query->where('status', $status);
        }

        $refunds = $query->paginate(20)->withQueryString();

        return view('dashboard.academy.refunds.index', compact('refunds', 'status'));
    }

    public function showRefundScreenshot(PrivateCourseRefund $refund)
    {
        $this->authorizeRefundScreenshotAccess($refund);

        // Prefer multi-screenshot relation; fall back to legacy single path.
        $shot = $refund->screenshots()->latest('id')->first();
        if ($shot && $shot->existsOnDisk()) {
            return Storage::disk('public')->response($shot->path);
        }

        abort_unless(
            filled($refund->screenshot_path)
            && Storage::disk('public')->exists($refund->screenshot_path),
            404
        );

        return Storage::disk('public')->response($refund->screenshot_path);
    }

    public function showRefundScreenshotFile(PrivateCourseRefundScreenshot $screenshot)
    {
        $screenshot->loadMissing('refund');
        abort_unless($screenshot->refund, 404);
        $this->authorizeRefundScreenshotAccess($screenshot->refund);
        abort_unless($screenshot->existsOnDisk(), 404);

        return Storage::disk('public')->response($screenshot->path);
    }

    public function uploadRefundScreenshot(Request $request, PrivateCourseRefund $refund)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        abort_unless($refund->canUploadScreenshots(), 422, __('messages.private_refund_upload_locked'));

        $data = $request->validate([
            'screenshot' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'kind' => 'required|in:pending,success,fail',
            'admin_note' => 'nullable|string|max:2000',
        ]);

        $path = $data['screenshot']->store('private-refunds', 'public');

        $refund->screenshots()->create([
            'path' => $path,
            'kind' => $data['kind'],
            'note' => $data['admin_note'] ?? null,
            'uploaded_by' => Auth::id(),
        ]);

        // Keep legacy columns in sync for older views/links; do NOT ask trainee yet.
        $refund->update([
            'screenshot_path' => $path,
            'screenshot_uploaded_at' => now(),
            'admin_id' => Auth::id(),
            'admin_note' => $data['admin_note'] ?? $refund->admin_note,
        ]);

        if ($refund->request) {
            $this->service->addEvent($refund->request, Auth::user(), 'refund_screenshot_uploaded', $data['admin_note'] ?? null, [
                'refund_id' => $refund->id,
                'kind' => $data['kind'],
            ]);
        }

        return back()->with('success', __('messages.private_refund_screenshot_uploaded'));
    }

    public function markRefundReadyForTrainee(PrivateCourseRefund $refund)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        try {
            $this->service->markRefundReadyForTrainee($refund, Auth::user());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['refund' => $e->getMessage()]);
        }

        return back()->with('success', __('messages.private_refund_marked_ready'));
    }

    protected function authorizeRefundScreenshotAccess(PrivateCourseRefund $refund): void
    {
        $user = Auth::user();
        abort_unless(
            $user && ($user->isAdmin() || (int) $refund->trainee_id === (int) $user->id),
            403
        );
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
