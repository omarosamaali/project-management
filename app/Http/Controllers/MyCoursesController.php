<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class MyCoursesController extends Controller
{
    public function index()
    {
        $myPayments = Payment::where('user_id', Auth::user()->id)
        ->whereNotNull('course_id')->with('course')->latest()->get();

        return view('dashboard.my_courses.index', compact('myPayments'));
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
