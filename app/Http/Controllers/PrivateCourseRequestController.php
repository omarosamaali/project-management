<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\PrivateCourseRequest;
use App\Models\User;
use App\Support\PrivateCourseRequestService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivateCourseRequestController extends Controller
{
    public function __construct(
        protected PrivateCourseRequestService $service,
    ) {}

    public function createForm(Course $course)
    {
        abort_unless($course->allows_private_requests, 404);
        abort_if($course->isPrivate() || filled($course->private_of_course_id), 404);

        if (! Auth::check()) {
            return redirect()->guest(
                \App\Support\AuthUi::loginUrl(['ui' => 'academy', 'redirect' => route('courses.private-request.create', $course)])
            );
        }

        abort_unless(Auth::user()->canLearnCourses(), 403);

        $privatePrice = $course->private_course_price ?? $course->price;
        $locale = app()->getLocale();
        $courseName = $locale === 'en'
            ? ($course->name_en ?: $course->name_ar)
            : $course->name_ar;

        return view('course.private-request-apply', compact('course', 'privatePrice', 'courseName'));
    }

    public function store(Request $request, Course $course)
    {
        abort_unless(Auth::check(), 401);
        abort_unless(Auth::user()->canLearnCourses(), 403);

        $data = $request->validate([
            'trainee_note' => 'nullable|string|max:2000',
            'accept_terms' => 'accepted',
        ], [
            'accept_terms.accepted' => __('messages.private_request_terms_required'),
        ]);

        $privateRequest = $this->service->createRequest(
            $course,
            Auth::user(),
            $data['trainee_note'] ?? null,
        );

        return redirect()
            ->route('private-requests.show', $privateRequest)
            ->with('success', __('messages.private_request_submitted'));
    }

    public function show(PrivateCourseRequest $privateRequest)
    {
        $this->authorizeView($privateRequest);

        $privateRequest->load([
            'sourceCourse.trainer',
            'privateCourse',
            'trainee',
            'trainer',
            'events.actor',
            'refund',
            'payment',
        ]);

        $user = Auth::user();
        $isTrainee = (int) $privateRequest->trainee_id === (int) $user->id;
        $isTrainer = $this->userIsRequestTrainer($user, $privateRequest);
        $isAdmin = $user->isAdmin();

        return view('course.private-request-show', compact(
            'privateRequest',
            'isTrainee',
            'isTrainer',
            'isAdmin',
        ));
    }

    public function acceptDates(PrivateCourseRequest $privateRequest)
    {
        $this->authorizeView($privateRequest);
        abort_unless((int) $privateRequest->trainee_id === (int) Auth::id(), 403);

        $this->service->traineeAcceptDates($privateRequest, Auth::user());

        return back()->with('success', __('messages.private_request_dates_accepted'));
    }

    public function requestDateChange(Request $request, PrivateCourseRequest $privateRequest)
    {
        $this->authorizeView($privateRequest);
        abort_unless((int) $privateRequest->trainee_id === (int) Auth::id(), 403);

        $data = $request->validate([
            'proposed_start_at' => 'required|date',
            'proposed_end_at' => 'required|date|after:proposed_start_at',
            'message' => 'nullable|string|max:2000',
        ]);

        $this->service->traineeRequestDateChange(
            $privateRequest,
            Auth::user(),
            Carbon::parse($data['proposed_start_at']),
            Carbon::parse($data['proposed_end_at']),
            $data['message'] ?? null,
        );

        return back()->with('success', __('messages.private_request_date_change_sent'));
    }

    public function pay(Request $request, PrivateCourseRequest $privateRequest)
    {
        $this->authorizeView($privateRequest);
        abort_unless((int) $privateRequest->trainee_id === (int) Auth::id(), 403);
        abort_unless($privateRequest->status === PrivateCourseRequest::STATUS_AWAITING_PAYMENT, 422);

        return app(ZiinaPaymentController::class)->createPrivateCoursePayment(
            $request->merge(['private_course_request_id' => $privateRequest->id])
        );
    }

    protected function authorizeView(PrivateCourseRequest $privateRequest): void
    {
        abort_unless(Auth::check(), 401);

        $user = Auth::user();
        if ($user->isAdmin()) {
            return;
        }

        if ((int) $privateRequest->trainee_id === (int) $user->id) {
            return;
        }

        if ($this->userIsRequestTrainer($user, $privateRequest)) {
            return;
        }

        abort(403, __('messages.private_request_forbidden'));
    }

    protected function userIsRequestTrainer(User $user, PrivateCourseRequest $privateRequest): bool
    {
        if (! $user->isTrainer() && ! $user->isAdmin()) {
            return false;
        }

        if ($privateRequest->trainer_id && (int) $privateRequest->trainer_id === (int) $user->id) {
            return true;
        }

        $privateRequest->loadMissing('sourceCourse');

        return $privateRequest->sourceCourse
            && (int) $privateRequest->sourceCourse->trainer_id === (int) $user->id;
    }
}
