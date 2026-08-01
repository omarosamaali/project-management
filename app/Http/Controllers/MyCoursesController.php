<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class MyCoursesController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()?->canLearnCourses()) {
            abort(403, 'دوراتي متاحة للمتدربين والمحاضرين والإدارة فقط.');
        }

        if ($redirect = $this->redirectToPendingExam()) {
            return $redirect;
        }

        $filter = $request->query('filter'); // null | active | upcoming | ended
        $now    = Carbon::now();

        $allPayments = Payment::where('user_id', Auth::id())
            ->whereNotNull('course_id')
            ->with(['course.units.items', 'course.dayExams', 'course.ratings'])
            ->latest()
            ->get()
            ->filter(fn ($p) => $p->course !== null)
            ->values();

        // Persist 100% recorded-path completion so ended filter stays in sync
        foreach ($allPayments as $payment) {
            $course = $payment->course;
            if (
                $course
                && $course->isRecorded()
                && !$payment->path_completed_at
                && $course->isPathFullyCompletedBy((int) $payment->user_id)
            ) {
                $payment->forceFill(['path_completed_at' => now()])->save();
            }
        }

        $myPayments = match ($filter) {
            'active'   => $allPayments->filter(fn ($p) => $p->isCourseActiveForLearner($now))->values(),
            'upcoming' => $allPayments->filter(fn ($p) => $p->isCourseUpcomingForLearner($now))->values(),
            'ended'    => $allPayments->filter(fn ($p) => $p->isCourseEndedForLearner($now))->values(),
            default    => $allPayments,
        };

        $totalCourses    = $allPayments->count();
        $activeCourses   = $allPayments->filter(fn ($p) => $p->isCourseActiveForLearner($now))->count();
        $upcomingCourses = $allPayments->filter(fn ($p) => $p->isCourseUpcomingForLearner($now))->count();
        $endedCourses    = $allPayments->filter(fn ($p) => $p->isCourseEndedForLearner($now))->count();

        $perPage = 9;
        $page = max(1, (int) $request->query('page', 1));
        $myPayments = new LengthAwarePaginator(
            $myPayments->forPage($page, $perPage)->values(),
            $myPayments->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('dashboard.my_courses.index', compact(
            'myPayments',
            'filter',
            'totalCourses',
            'activeCourses',
            'upcomingCourses',
            'endedCourses'
        ));
    }

    public function show($id)
    {
        if (!Auth::user()?->canLearnCourses()) {
            abort(403, 'دوراتي متاحة للمتدربين والمحاضرين والإدارة فقط.');
        }

        if ($redirect = $this->redirectToPendingExam()) {
            return $redirect;
        }

        $payment = Payment::where('user_id', Auth::user()->id)
            ->where('id', $id)->with(['course.dayExams', 'course.ratings', 'course.dayExamAttempts'])->firstOrFail();

        return view('dashboard.my_courses.show', compact('payment'));
    }

    /**
     * If the student is attended and an exam is currently running, send them there.
     */
    protected function redirectToPendingExam()
    {
        $pending = CourseExamController::findPendingExamPayment(Auth::id());

        if (!$pending) {
            return null;
        }

        return redirect()->route('dashboard.courses.exam.take', [
            $pending['payment']->course_id,
            $pending['dayExam']->id,
        ]);
    }

    /**
     * Open a course action button (needs_login) inside the dashboard layout via iframe.
     */
    public function showButton($paymentId, $buttonIndex)
    {
        $payment = Payment::where('user_id', Auth::id())
            ->where('id', $paymentId)
            ->with('course')
            ->firstOrFail();

        $course = $payment->course;
        abort_unless($course, 404);

        $buttons = collect($course->buttons ?? [])->values();
        $button = $buttons->get((int) $buttonIndex);

        abort_unless(
            $button
                && !empty($button['needs_login'])
                && !empty($button['link']),
            404
        );

        $buttonTitle = app()->getLocale() === 'en'
            ? ($button['text_en'] ?? $button['text_ar'] ?? 'محتوى')
            : ($button['text_ar'] ?? $button['text_en'] ?? 'محتوى');

        return view('dashboard.my_courses.button', compact(
            'payment',
            'course',
            'button',
            'buttonTitle',
            'buttonIndex'
        ));
    }

    public function showInvoice($payment_id)
    {
        // بنجيب الدفع مع الكورس بتاعه فقط
        $payment = Payment::with('course')->findOrFail($payment_id);

        return view('dashboard.my_courses.invoice', [
            'payment' => $payment,
            'course'  => $payment->course, // ده اللي هيعرض بيانات الدورة
        ]);
    }
}
