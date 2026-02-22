<?php

use App\Http\Controllers\Dashboard\RequestsController;
use App\Http\Controllers\Dashboard\SpecialRequestMessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpecialRequestController;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZiinaPaymentController;
use App\Http\Controllers\ProjectMeetingController;
use App\Http\Controllers\ProjectBudgetController;
use App\Http\Controllers\RequestMessageController;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\CourseController;

Route::patch('special-request/payment/{payment}/mark-paid', [SpecialRequestController::class, 'markPaymentAsPaid'])
    ->name('special-request.payment.mark-paid');

Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {

    // راوت تحديث حالة الحضور (Toggle)
    Route::post('/payments/{payment}/toggle-attendance', [CourseController::class, 'toggleAttendance'])
        ->name('courses.toggle-attendance');

    // راوت عرض الشهادة
    Route::get('/payments/{payment}/certificate', [CourseController::class, 'showCertificate'])
        ->name('courses.certificate');
});
Route::post('/resend-otp/{type}', [App\Http\Controllers\Auth\OTPController::class, 'resend'])->name('otp.resend');    
Route::middleware(['auth'])->group(function () {
    // صفحة الـ OTP
    Route::get('/verify-otp', [OTPController::class, 'showVerifyPage'])->name('otp.verify');
    Route::post('/verify-otp/whatsapp', [OTPController::class, 'verifyWhatsapp'])->name('otp.whatsapp.check');
    Route::post('/verify-otp/email', [OTPController::class, 'verifyEmail'])->name('otp.email.check');
});

// تطبيق الحماية على صفحات الداشبورد
Route::middleware(['auth', \App\Http\Middleware\CheckOtpVerification::class])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::post('/request-messages', [RequestMessageController::class, 'store'])
    ->name('dashboard.request-messages.store');

Route::post('special-request-messages/store', [SpecialRequestMessageController::class, 'store'])
    ->name('dashboard.special-request-messages.store');

Route::post('/requests/{id}/update-budget', [ProjectBudgetController::class, 'updateBudget'])->name('requests.update-budget');
Route::middleware(['auth'])->group(function () {

    Route::get('/special-requests/{specialRequest}/payment/{payment}/invoice', function ($specialRequestId, $paymentId) {
        $specialRequest = \App\Models\SpecialRequest::findOrFail($specialRequestId);
        $payment = \App\Models\Payment::findOrFail($paymentId);
        $installmentId = request()->get('installment_id');
        $installment = null;
        if ($installmentId) {
            $installment = \App\Models\RequestPayment::find($installmentId);
        }

        if (!$installment) {
            $installment = \App\Models\RequestPayment::where('special_request_id', $specialRequest->id)
                ->where('status', 'paid')
                ->orderBy('paid_at', 'desc')
                ->first();
        }

        return view('special-request.invoice', compact('specialRequest', 'payment', 'installment'));
    })->name('special-request.payment.invoice')->middleware('auth');

    
    Route::get('/special-requests/{specialRequest}/payment/{payment}/invoice', function ($specialRequestId, $paymentId) {
        $specialRequest = \App\Models\SpecialRequest::findOrFail($specialRequestId);
        $payment = \App\Models\Payment::findOrFail($paymentId);
        $installment = \App\Models\RequestPayment::where('id', $payment->payment_id)->first();
        return view('special-request.invoice', compact('specialRequest', 'payment', 'installment'));
    })->name('special-request.payment.invoice')->middleware('auth');

    Route::post('/payments/{payment}/ziina-pay', [ZiinaPaymentController::class, 'initiateInstallmentPayment'])
        ->name('ziina.installment.pay')
        ->middleware(['auth', \App\Http\Middleware\SetLocale::class]);  // هنا الكلاس الكامل
    // صفحة العودة بعد الدفع الناجح للدفعة
    Route::get('/payment/installment/return', [ZiinaPaymentController::class, 'handleInstallmentReturn'])
    ->name('payment.installment.return');
    Route::post('/proposals/{id}/accept', [ProjectMeetingController::class, 'accept'])->name('proposals.accept');
    Route::post('/proposals/{id}/reject', [ProjectMeetingController::class, 'reject'])->name('proposals.reject');
    Route::post('/project-meetings', [ProjectMeetingController::class, 'store'])->name('meetings.store');
    Route::patch('/project-meetings/{meeting}/status', [ProjectMeetingController::class, 'updateStatus'])->name('meetings.updateStatus');
    Route::put('/project-meetings/{meeting}', [ProjectMeetingController::class, 'update'])->name('meetings.update');
    Route::delete('/project-meetings/{meeting}', [ProjectMeetingController::class, 'destroy'])->name('meetings.destroy');
});

// Ziina Payment
Route::middleware(['auth'])->group(function () {
    Route::post('/payment/create', [ZiinaPaymentController::class, 'createPayment'])->name('payment.create');
    Route::get('/payment/success', [ZiinaPaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [ZiinaPaymentController::class, 'cancel'])->name('payment.cancel');
    Route::post('/payment/special-request/create', [ZiinaPaymentController::class, 'createSpecialRequestPayment'])
        ->name('payment.special-request.create');

    Route::get('/payment/special-request/return', [ZiinaPaymentController::class, 'handleReturn'])
        ->name('payment.special-request.return');
        
    });

Route::post('/payment/special-request/callback', [ZiinaPaymentController::class, 'handleCallback'])
->name('payment.special-request.callback');
Route::post('/payment/webhook', [ZiinaPaymentController::class, 'webhook'])->name('payment.webhook');

// Switch Language
Route::get('/lang/{lang}', function ($lang) {
    session()->put(['lang' => $lang]);
    return redirect()->back();
})->name('lang.switch');

// System Routes
Route::get('/', [SystemController::class, 'index'])->name('system.index');
Route::get('/system/{system}', [SystemController::class, 'show'])->name('system.show');
Route::post('/system/request', [RequestsController::class, 'clientStore'])->name('dashboard.requests.clientStore');

// Special Requests
Route::get('/special-request/index', [SpecialRequestController::class, 'index'])->name('special-request.index');
Route::post('/special-request/store', [SpecialRequestController::class, 'store'])->name('special-request.store');
Route::get('/special-request/show', [SpecialRequestController::class, 'show'])->name('special-request.show');
Route::get('/special-request/edit', [SpecialRequestController::class, 'edit'])->name('special-request.edit');
Route::delete('/special-request/{specialRequest}', [SpecialRequestController::class, 'destroy'])
    ->name('show.special-request.destroy');
Route::get('/special-request/show-special-request/{specialRequest}', [SpecialRequestController::class, 'showSpecialRequest'])
->name('special-request.show-special-request');
Route::resource('special-request', SpecialRequestController::class)->names('special-request')->except(['show']);

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__ .'/dashboard.php';
