<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MyCoursesController extends Controller
{
    public function index(Request $request)
    {
        if ($redirect = $this->redirectToPendingExam()) {
            return $redirect;
        }

        $filter = $request->query('filter'); // null | active | upcoming | ended
        $now    = Carbon::now();

        $allPayments = Payment::where('user_id', Auth::id())
            ->whereNotNull('course_id')
            ->with('course')
            ->latest()
            ->get();

        $myPayments = match ($filter) {
            'active'   => $allPayments->filter(fn($p) => $p->course
                                && $now->between(
                                    Carbon::parse($p->course->start_date),
                                    Carbon::parse($p->course->end_date)
                                )),
            'upcoming' => $allPayments->filter(fn($p) => $p->course
                                && $now->lt(Carbon::parse($p->course->start_date))),
            'ended'    => $allPayments->filter(fn($p) => $p->course
                                && $now->gt(Carbon::parse($p->course->end_date))),
            default    => $allPayments,
        };

        $activeCourses   = $allPayments->filter(fn($p) => $p->course
                                && $now->between(
                                    Carbon::parse($p->course->start_date),
                                    Carbon::parse($p->course->end_date)
                                ))->count();
        $upcomingCourses = $allPayments->filter(fn($p) => $p->course
                                && $now->lt(Carbon::parse($p->course->start_date)))->count();
        $endedCourses    = $allPayments->filter(fn($p) => $p->course
                                && $now->gt(Carbon::parse($p->course->end_date)))->count();

        return view('dashboard.my_courses.index', compact(
            'myPayments',
            'filter',
            'activeCourses',
            'upcomingCourses',
            'endedCourses'
        ));
    }

    public function show($id)
    {
        if ($redirect = $this->redirectToPendingExam()) {
            return $redirect;
        }

        $payment = Payment::where('user_id', Auth::user()->id)
            ->where('id', $id)->with('course')->firstOrFail();

        return view('dashboard.my_courses.show', compact('payment'));
    }

    /**
     * If the student is attended and an exam is currently running, send them there.
     */
    protected function redirectToPendingExam()
    {
        $payment = CourseExamController::findPendingExamPayment(Auth::id());

        if (!$payment) {
            return null;
        }

        return redirect()->route('dashboard.courses.exam.take', $payment->course_id);
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
