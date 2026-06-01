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
        $payment = Payment::where('user_id', Auth::user()->id)
            ->where('id', $id)->with('course')->firstOrFail();

        return view('dashboard.my_courses.show', compact('payment'));
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
