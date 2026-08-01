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
use App\Http\Controllers\CourseLectureController;

Route::get('/courses/{course}/video/stream', [CourseController::class, 'streamPromo'])
    ->name('courses.video.stream');

Route::patch('special-request/payment/{payment}/mark-paid', [SpecialRequestController::class, 'markPaymentAsPaid'])
    ->name('special-request.payment.mark-paid');

Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {

    // راوت تحديث حالة الحضور (Toggle)
    Route::post('/payments/{payment}/toggle-attendance', [CourseController::class, 'toggleAttendance'])
        ->name('courses.toggle-attendance');

    // تحضير جماعي للمشتركين
    Route::post('/courses/{course}/ratings/{rating}/toggle-featured', [CourseController::class, 'toggleFeaturedRating'])
        ->name('courses.ratings.toggle-featured');
    Route::post('/courses/{course}/bulk-attendance', [CourseController::class, 'bulkAttendance'])
        ->name('courses.bulk-attendance');

    // راوت عرض الشهادة
    Route::get('/payments/{payment}/certificate', [CourseController::class, 'showCertificate'])
        ->name('courses.certificate');

    // اختبارات أيام الدورة
    Route::post('/courses/{course}/day-exams/{dayExam}/start', [CourseController::class, 'startDayExam'])
        ->name('courses.day-exams.start');
    Route::post('/courses/{course}/day-exams/{dayExam}/end', [CourseController::class, 'endDayExam'])
        ->name('courses.day-exams.end');
    Route::post('/courses/{course}/day-exams/{dayExam}/skip', [CourseController::class, 'skipDayExam'])
        ->name('courses.day-exams.skip');
    Route::post('/courses/{course}/start-exam', [CourseController::class, 'startExam'])
        ->name('courses.start-exam');
    Route::post('/courses/{course}/end-exam', [CourseController::class, 'endExam'])
        ->name('courses.end-exam');
    Route::get('/courses/{course}/exam-statuses', [CourseController::class, 'examStatuses'])
        ->name('courses.exam-statuses');
    Route::get('/exam/pending-check', [\App\Http\Controllers\CourseExamController::class, 'pendingCheck'])
        ->name('courses.exam.pending-check');
    Route::get('/courses/{course}/day-exams/{dayExam}/exam', [\App\Http\Controllers\CourseExamController::class, 'take'])
        ->name('courses.exam.take');
    Route::post('/courses/{course}/day-exams/{dayExam}/exam', [\App\Http\Controllers\CourseExamController::class, 'submit'])
        ->name('courses.exam.submit');
    Route::get('/courses/{course}/day-exams/{dayExam}/exam/result', [\App\Http\Controllers\CourseExamController::class, 'result'])
        ->name('courses.exam.result');
    Route::get('/courses/{course}/rating', [\App\Http\Controllers\CourseRatingController::class, 'show'])
        ->name('courses.rating');
    Route::post('/courses/{course}/rating', [\App\Http\Controllers\CourseRatingController::class, 'store'])
        ->name('courses.rating.store');

    // غرفة المحاضرة الأونلاين + نقاش مباشر / أرشيف
    Route::get('/my_courses/{payment}/lecture', [CourseLectureController::class, 'show'])
        ->name('my_courses.lecture');
    Route::get('/courses/{course}/lecture', [CourseLectureController::class, 'showForManager'])
        ->name('courses.lecture');
    Route::get('/courses/{course}/chat', [CourseLectureController::class, 'archive'])
        ->name('courses.chat-archive');
    Route::get('/courses/{course}/chat/messages', [CourseLectureController::class, 'messages'])
        ->name('courses.chat.messages');
    Route::post('/courses/{course}/chat/messages', [CourseLectureController::class, 'storeMessage'])
        ->name('courses.chat.store');
    Route::post('/courses/{course}/chat/messages/{message}/hide', [CourseLectureController::class, 'hideMessage'])
        ->name('courses.chat.hide');
    Route::post('/courses/{course}/chat/messages/{message}/unhide', [CourseLectureController::class, 'unhideMessage'])
        ->name('courses.chat.unhide');
    Route::post('/courses/{course}/chat/block', [CourseLectureController::class, 'blockUser'])
        ->name('courses.chat.block');
    Route::post('/courses/{course}/chat/unblock/{userId}', [CourseLectureController::class, 'unblockUser'])
        ->name('courses.chat.unblock');
    Route::post('/courses/{course}/chat/lock', [CourseLectureController::class, 'toggleChatLock'])
        ->name('courses.chat.lock');

    // المسار التعليمي للدورات المسجّلة
    Route::get('/my_courses/{payment}/path', [\App\Http\Controllers\CoursePathController::class, 'show'])
        ->name('my_courses.path');
    Route::post('/courses/{course}/path/items/{item}/progress', [\App\Http\Controllers\CoursePathController::class, 'progress'])
        ->name('courses.path.progress');
    Route::get('/courses/{course}/path/items/{item}/stream', [\App\Http\Controllers\CoursePathController::class, 'stream'])
        ->name('courses.path.stream');
    Route::post('/courses/{course}/path/items/{item}/thumbnail', [\App\Http\Controllers\CoursePathController::class, 'saveThumbnail'])
        ->name('courses.path.thumbnail');
    Route::post('/courses/{course}/path/items/{item}/exam', [\App\Http\Controllers\CoursePathController::class, 'submitExam'])
        ->name('courses.path.exam');
});
Route::middleware(['auth'])->group(function () {
    Route::post('/resend-otp/{type}', [OTPController::class, 'resend'])->name('otp.resend');
    Route::get('/verify-otp', [OTPController::class, 'showVerifyPage'])->name('otp.verify');
    Route::post('/verify-otp/whatsapp', [OTPController::class, 'verifyWhatsapp'])->name('otp.whatsapp.check');
    Route::post('/verify-otp/email', [OTPController::class, 'verifyEmail'])->name('otp.email.check');
});

Route::post('/request-messages', [RequestMessageController::class, 'store'])
    ->name('dashboard.request-messages.store');

Route::post('special-request-messages/store', [SpecialRequestMessageController::class, 'store'])
    ->name('dashboard.special-request-messages.store');

Route::post('/requests/{id}/update-budget', [ProjectBudgetController::class, 'updateBudget'])->name('requests.update-budget');
Route::middleware(['auth'])->group(function () {

    // Route::get('/special-requests/{specialRequest}/payment/{payment}/invoice', function ($specialRequestId, $paymentId) {
    //     $specialRequest = \App\Models\SpecialRequest::findOrFail($specialRequestId);
    //     $payment = \App\Models\Payment::findOrFail($paymentId);
    //     $installmentId = request()->get('installment_id');
    //     $installment = null;
    //     if ($installmentId) {
    //         $installment = \App\Models\RequestPayment::find($installmentId);
    //     }

    //     if (!$installment) {
    //         $installment = \App\Models\RequestPayment::where('special_request_id', $specialRequest->id)
    //             ->where('status', 'paid')
    //             ->orderBy('paid_at', 'desc')
    //             ->first();
    //     }

    //     return view('special-request.invoice', compact('specialRequest', 'payment', 'installment'));
    // })->name('special-request.payment.invoice')->middleware('auth');

    Route::get('/special-requests/{specialRequest}/payment/{payment}/invoice', function ($specialRequestId, $paymentId) {
        $specialRequest = \App\Models\SpecialRequest::findOrFail($specialRequestId);
        $payment = \App\Models\Payment::findOrFail($paymentId);
        $installment = \App\Models\RequestPayment::find(request('installment_id'));

        return view('special-request.invoice', compact('specialRequest', 'payment', 'installment'));
    })->name('special-request.payment.invoice')->middleware('auth');

    Route::get('/special-requests/{specialRequest}/installments/{installment}/invoice', function ($specialRequestId, $installmentId) {
        $specialRequest = \App\Models\SpecialRequest::findOrFail($specialRequestId);
        $installment = \App\Models\RequestPayment::where('id', $installmentId)
            ->where('special_request_id', $specialRequestId)
            ->firstOrFail();

        $payment = \App\Support\InstallmentPaymentService::findZiinaPaymentForInstallment($installment)
            ?? \App\Support\InstallmentPaymentService::buildInvoicePaymentPreview($installment);

        return view('special-request.invoice', compact('specialRequest', 'payment', 'installment'));
    })->name('special-request.installment.invoice')->middleware('auth');

    
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
Route::get('/academy', [\App\Http\Controllers\AcademyController::class, 'index'])->name('academy.index');
Route::get('/academy/courses', [\App\Http\Controllers\AcademyController::class, 'courses'])->name('academy.courses');
Route::get('/academy/categories/{category}', [\App\Http\Controllers\AcademyController::class, 'category'])->name('academy.category');
Route::get('/academy/trainers', [\App\Http\Controllers\AcademyController::class, 'trainers'])->name('academy.trainers.index');
Route::get('/academy/trainers/{trainer}', [\App\Http\Controllers\AcademyController::class, 'trainer'])->name('academy.trainers.show');
Route::get('/academy/wishlist', [\App\Http\Controllers\CourseWishlistController::class, 'index'])
    ->middleware('auth')
    ->name('academy.wishlist.index');
Route::post('/academy/courses/{course}/wishlist', [\App\Http\Controllers\CourseWishlistController::class, 'toggle'])
    ->name('academy.wishlist.toggle');
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

// Dashboard — قمرة القيادة
Route::get('/dashboard', [\App\Http\Controllers\Dashboard\CockpitController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__ .'/dashboard.php';
