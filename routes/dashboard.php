<?php

use App\Http\Controllers\Dashboard\SystemController;
use App\Http\Controllers\Dashboard\PartnerController;
use App\Http\Controllers\Dashboard\ClientController;
use App\Http\Controllers\Dashboard\AcademyAccountController;
use App\Http\Controllers\Dashboard\AcademyProfitController;
use App\Http\Controllers\Dashboard\AcademyRatingController;
use App\Http\Controllers\Dashboard\AcademySettingsController;
use App\Http\Controllers\Dashboard\PayoutMethodController;
use App\Http\Controllers\Dashboard\TrainerPaymentProfileController;
use App\Http\Controllers\Dashboard\TrainerCashoutController;
use App\Http\Controllers\Dashboard\CurrencyRateController;
use App\Http\Controllers\Dashboard\RequestsController;
use App\Http\Controllers\Dashboard\SupportController;
use App\Http\Controllers\Dashboard\TechnicalSupportController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\RatingController;
use App\Http\Controllers\Dashboard\PerformanceController;
use App\Http\Controllers\Dashboard\WithdrawalRequestsController;
use App\Http\Controllers\Dashboard\SpecialRequestController;
use App\Http\Controllers\Dashboard\CourseCategoryController;
use App\Http\Controllers\Dashboard\ServiceController;
use App\Http\Controllers\Dashboard\MyServiceController;
use App\Http\Controllers\Dashboard\LogoController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\ExpensesRequestController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ProjectFileController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\PartnerRegistrationController;
use App\Http\Controllers\Dashboard\MyServicesController;
use App\Http\Controllers\Admin\AvailableServiceController;
use App\Http\Controllers\Admin\NewProjectController;
use App\Http\Controllers\Admin\KbCategoryController;
use App\Http\Controllers\Admin\KnowledgeBaseController;
use App\Http\Controllers\Admin\SessionRequestController;
use App\Http\Controllers\ProjectManagerController;
use App\Http\Controllers\IssueCommentController;
use App\Http\Controllers\WorkTimeController;
use App\Http\Controllers\WorkTimeCalendarController;
use App\Http\Controllers\RequestFileController;
use App\Http\Controllers\PartnerSystemController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\AdminRemarkController;
use App\Http\Controllers\CreateRequestController;
use App\Http\Controllers\Dashboard\AdjustmentController;
use App\Http\Controllers\Dashboard\HolidayController;
use App\Http\Controllers\Dashboard\IndependentPartnerController;
use App\Http\Controllers\Dashboard\MyStoreController;
use App\Http\Controllers\ProjectMeetingController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ZiinaPaymentController;
use App\Http\Controllers\MyCoursesController;
use App\Http\Controllers\EducationalResourceController;
use App\Http\Controllers\NotificationController;
use App\Models\MyStore;

Route::patch('/dashboard/my-store/{id}/update-status', [MyStoreController::class, 'updateStatus'])
    ->name('dashboard.my-store.update-status');

Route::post('/send-whatsapp-otp', [App\Http\Controllers\PartnerRegistrationController::class, 'sendOtp'])->name('send.otp');
Route::post('/send-otp', [PartnerRegistrationController::class, 'sendOtp'])->name('send.whatsapp.otp');
Route::middleware(['auth'])->group(function () {
    Route::get('project-meetings', [ProjectMeetingController::class, 'index'])
        ->name('meetings.index');
    Route::post('project-meetings', [ProjectMeetingController::class, 'store'])
        ->name('meetings.store');
    Route::patch('project-meetings/{meeting}', [ProjectMeetingController::class, 'update'])
        ->name('meetings.update');
    Route::delete('project-meetings/{meeting}', [ProjectMeetingController::class, 'destroy'])
        ->name('meetings.destroy');
    Route::patch('project-meetings/{meeting}/status', [ProjectMeetingController::class, 'updateStatus'])
        ->name('meetings.updateStatus');
});

// ملف web.php
Route::middleware(['auth'])->group(function () {
    
    Route::post('/project-meetings', [ProjectMeetingController::class, 'store'])->name('meetings.store');
    Route::patch('/project-meetings/{meeting}/status', [ProjectMeetingController::class, 'updateStatus'])->name('meetings.updateStatus');
    
    Route::get('dashboard.requests.create-request', [CreateRequestController::class, 'createRequest'])
    ->name('dashboard.requests.create-request');
    
    Route::post('dashboard.requests.post-request', [CreateRequestController::class, 'postRequest'])
    ->name('dashboard.requests.post-request');
    
    Route::post('/special-request/{special_request}/add-note', [SpecialRequestController::class, 'addNote'])
    ->name('dashboard.special-request.add-note');
});
    
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('work-times/calendar/events', [WorkTimeCalendarController::class, 'events'])->name('work-times.calendar.events');
    Route::get('work-times/calendar', [WorkTimeCalendarController::class, 'index'])->name('work-times.calendar');
    Route::resource('work-times', WorkTimeController::class);
    Route::post('work-times/quick-action', [WorkTimeController::class, 'quickAction'])->name('work-times.quick-action');
    Route::get('work-times/my-status', [WorkTimeController::class, 'myStatus'])->name('work-times.my-status');
    Route::get('work-times/country-time', [WorkTimeController::class, 'countryTime'])->name('work-times.country-time');
    Route::resource('educational_resources', EducationalResourceController::class);

    Route::post('special-request/{specialRequest}/store-stage', [SpecialRequestController::class, 'storeStage'])
        ->name('special-request.store-stage');

    Route::post('special-request/{specialRequest}/store--stage', [SpecialRequestController::class, 'storeStage1'])
        ->name('special-request.store1-stage');

    Route::delete('special-request/stages/{stage}', [SpecialRequestController::class, 'destroyStage'])
        ->name('special-request.destroy-stage');
});

Route::post('sessions/{session}/update-status', [SessionRequestController::class, 'updateParticipantStatus'])
    ->name('dashboard.sessions.updateStatus');
    
// مسارات الأخطاء (Issues)
Route::put('/issues/{issue}', [IssueController::class, 'update'])->name('issues.update');
Route::delete('/issues/{issue}', [IssueController::class, 'destroy'])->name('issues.destroy');
Route::patch('/issues/{issue}/status', [IssueController::class, 'updateStatus'])->name('issues.update-status');

// مسارات التعليقات (Comments)
Route::post('/issues/{issue}/comments', [IssueCommentController::class, 'store'])->name('issue-comments.store');
Route::post('/issues/{issue}/comments/{comment}/mark-solution', [IssueCommentController::class, 'markAsSolution'])->name('issue-comments.mark-solution');
Route::post('/issues/{issue}/unmark-solution', [IssueCommentController::class, 'unmarkSolution'])->name('issue-comments.unmark-solution');
Route::delete('/comments/{comment}', [IssueCommentController::class, 'destroy'])->name('issue-comments.destroy');

// Knowledge Base
Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::resource('kb_categories', KbCategoryController::class);
    Route::resource('my_courses', MyCoursesController::class);
    Route::get('my_courses/{payment}/button/{button}', [MyCoursesController::class, 'showButton'])
        ->whereNumber(['payment', 'button'])
        ->name('my_courses.button');
    Route::resource('kb', KnowledgeBaseController::class);
    Route::resource('sessions', SessionRequestController::class)->names('sessions');
});
Route::get('/dashboard/payment/invoice/{payment_id}', [MyCoursesController::class, 'showInvoice'])->name('dashboard.payment.invoice');
// My Services
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/{id}/payments', [SystemController::class, 'payments'])->name('systems.payments');
    Route::resource('available_services', AvailableServiceController::class)->names('available_services');
    Route::get('/my-services', [MyServicesController::class, 'index'])->name('my_service.index');
    Route::post('/my-services', [MyServicesController::class, 'store'])->name('my_service.store');
    Route::get('/my-services/show', [MyServicesController::class, 'show'])->name('my_service.show');
});

Route::name('dashboard.')->prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('courses/resolve-video-duration', [CourseController::class, 'resolveVideoDuration'])
        ->name('courses.resolve-video-duration');
    Route::resource('courses', CourseController::class);

    Route::resource('course-categories', CourseCategoryController::class)->except(['show']);
});

// Public course page is registered in web.php (academy domain when split is enabled).
if (! \App\Support\AppDomains::enabled()) {
    Route::get('/courses/{course}', [CourseController::class, 'userShow'])->name('courses.show');
}

// System Routes
Route::middleware('auth')->group(function () {
    Route::resource('logos', LogoController::class)->names('dashboard.logos');
    Route::post('/request-files', [RequestFileController::class, 'store'])->name('request-files.store');
    Route::delete('/request-files/{file}', [RequestFileController::class, 'destroy'])->name('request-files.destroy');
    Route::post('/dashboard/requests/{id}/add-note', [RequestsController::class, 'addNote'])->name('dashboard.requests.add-note');
    Route::delete('/dashboard/requests/note/{id}', [RequestsController::class, 'destroyNote'])->name('dashboard.requests.destroy-note');
    Route::post('/dashboard/requests/{id}/update-budget', [RequestsController::class, 'updateProjectBudget'])->name('dashboard.requests.update-budget');
    Route::post('/requests/{id}/deliver', [RequestsController::class, 'deliver'])->name('dashboard.requests.deliver');
    Route::get('special-requests/{id}/deliver', [SpecialRequestController::class, 'deliverProject'])->name('dashboard.special-requests.deliver');
    Route::get('special-requests/{id}/receive', [SpecialRequestController::class, 'receiveProject'])->name('dashboard.special-requests.receive');
    Route::resource('new_project', NewProjectController::class)->names('dashboard.new_project');
    Route::resource('project_manager', ProjectManagerController::class)->names('dashboard.project_manager');
    Route::post('new_project/{id}/proposal', [NewProjectController::class, 'storeProposal'])->name('dashboard.new_project.store_proposal');
    Route::resource('systems', SystemController::class)->names('dashboard.systems');
    Route::resource('my-store', MyStoreController::class)->names('dashboard.my-store');
    // Route::resource('courses', CourseController::class)->names('dashboard.courses');
    Route::get('courses/{course}/payments', [CourseController::class, 'payments'])
        ->name('dashboard.courses.payments');
    // دفع الدورة (إنشاء payment intent)
    Route::post('/course/payment/create', [ZiinaPaymentController::class, 'createCoursePayment'])->name('course.payment.create');
    Route::get('/course/payment/success', [ZiinaPaymentController::class, 'courseSuccess'])->name('course.payment.success');
    Route::get('/course/payment/cancel', [ZiinaPaymentController::class, 'courseCancel'])->name('course.payment.cancel');
    Route::post('/course/private/payment/create', [ZiinaPaymentController::class, 'createPrivateCoursePayment'])->name('course.private.payment.create');
    Route::get('/course/private/payment/success', [ZiinaPaymentController::class, 'privateCourseSuccess'])->name('course.private.payment.success');
    Route::get('/course/private/payment/cancel', [ZiinaPaymentController::class, 'privateCourseCancel'])->name('course.private.payment.cancel');

    // Keep academy admin/cockpit routes under /dashboard/... so domain split does not
    // send /academy/* to the public academy host (403 / wrong page).
    Route::prefix('dashboard')->group(function () {
    Route::get('academy/my-private-requests', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'traineeIndex'])
        ->name('dashboard.academy.private-requests.trainee-index');
    Route::get('academy/private-requests/inbox', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'trainerInbox'])
        ->name('dashboard.academy.private-requests.trainer-inbox');
    Route::get('academy/private-requests/unassigned', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'adminUnassigned'])
        ->name('dashboard.academy.private-requests.admin-unassigned');
    Route::get('academy/private-requests', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'adminIndex'])
        ->name('dashboard.academy.private-requests.admin-index');
    Route::post('academy/private-requests/{privateRequest}/meeting-link', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'updateMeetingLink'])
        ->name('dashboard.academy.private-requests.meeting-link');
    Route::post('academy/private-requests/{privateRequest}/approve', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'approve'])
        ->name('dashboard.academy.private-requests.approve');
    Route::post('academy/private-requests/{privateRequest}/reject', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'reject'])
        ->name('dashboard.academy.private-requests.reject');
    Route::post('academy/private-requests/{privateRequest}/respond-date-change', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'respondToDateChange'])
        ->name('dashboard.academy.private-requests.respond-date-change');
    Route::post('academy/private-requests/{privateRequest}/block', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'block'])
        ->name('dashboard.academy.private-requests.block');
    Route::get('academy/private-refunds', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'refundsIndex'])
        ->name('dashboard.academy.private-refunds.index');
    Route::get('academy/private-refunds/{refund}/screenshot', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'showRefundScreenshot'])
        ->name('dashboard.academy.private-refunds.screenshot');
    Route::get('academy/private-refunds/files/{screenshot}', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'showRefundScreenshotFile'])
        ->name('dashboard.academy.private-refunds.screenshot-file');
    Route::post('academy/private-refunds/{refund}/screenshot', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'uploadRefundScreenshot'])
        ->name('dashboard.academy.private-refunds.upload-screenshot');
    Route::post('academy/private-refunds/{refund}/mark-ready', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'markRefundReadyForTrainee'])
        ->name('dashboard.academy.private-refunds.mark-ready');
    Route::post('academy/private-refunds/{refund}/confirm', [\App\Http\Controllers\Dashboard\PrivateCourseRequestDashboardController::class, 'confirmRefund'])
        ->name('dashboard.academy.private-refunds.confirm');

    // Trainer payout methods (admin)
    Route::prefix('academy')->group(function () {
        Route::resource('payout-methods', PayoutMethodController::class)
            ->except(['show'])
            ->names('dashboard.academy.payout-methods');
    });

    // Trainer payment profile
    Route::get('academy/payment-profile', [TrainerPaymentProfileController::class, 'edit'])
        ->name('dashboard.academy.payment-profile.edit');
    Route::put('academy/payment-profile', [TrainerPaymentProfileController::class, 'update'])
        ->name('dashboard.academy.payment-profile.update');

    // Trainer cashout requests
    Route::post('academy/cashouts', [TrainerCashoutController::class, 'store'])
        ->name('dashboard.academy.cashouts.store');
    Route::post('academy/cashouts/{cashout}/confirm', [TrainerCashoutController::class, 'confirm'])
        ->name('dashboard.academy.cashouts.confirm');
    Route::get('academy/cashouts', [TrainerCashoutController::class, 'adminIndex'])
        ->name('dashboard.academy.cashouts.index');
    Route::get('academy/cashouts/files/{screenshot}', [TrainerCashoutController::class, 'showScreenshotFile'])
        ->name('dashboard.academy.cashouts.screenshot-file');
    Route::post('academy/cashouts/{cashout}/screenshot', [TrainerCashoutController::class, 'uploadScreenshot'])
        ->name('dashboard.academy.cashouts.upload-screenshot');
    Route::post('academy/cashouts/{cashout}/mark-ready', [TrainerCashoutController::class, 'markReadyForTrainer'])
        ->name('dashboard.academy.cashouts.mark-ready');
    Route::post('academy/cashouts/{cashout}/reject', [TrainerCashoutController::class, 'reject'])
        ->name('dashboard.academy.cashouts.reject');

    Route::get('academy/off-days', [\App\Http\Controllers\Dashboard\TrainerOffDayController::class, 'index'])
        ->name('dashboard.academy.off-days.index');
    Route::post('academy/off-days', [\App\Http\Controllers\Dashboard\TrainerOffDayController::class, 'store'])
        ->name('dashboard.academy.off-days.store');
    Route::delete('academy/off-days/{offDay}', [\App\Http\Controllers\Dashboard\TrainerOffDayController::class, 'destroy'])
        ->name('dashboard.academy.off-days.destroy');
    }); // end dashboard prefix (academy cockpit routes)

    Route::post('/payments/{payment}/special-certificate', [CourseController::class, 'uploadSpecialCertificate'])
        ->name('dashboard.courses.special-certificate.upload');
    Route::get('/payments/{payment}/special-certificate', [CourseController::class, 'downloadSpecialCertificate'])
        ->name('dashboard.courses.special-certificate.download');
        
    Route::get('/stores/{store}', [CourseController::class, 'userShowStore'])->name('stores.show');
    Route::get('my-profile', [PartnerController::class, 'myProfile'])->name('dashboard.my-profile');
    Route::resource('partners', PartnerController::class)->names('dashboard.partners');
    Route::resource('clients', ClientController::class)->names('dashboard.clients');

    // Academy: trainers / trainees / ratings (admin)
    foreach (['trainers' => 'trainer', 'trainees' => 'trainee'] as $plural => $role) {
        Route::get($plural, [AcademyAccountController::class, 'index'])
            ->defaults('role', $role)
            ->name("dashboard.{$plural}.index");
        Route::get("{$plural}/create", [AcademyAccountController::class, 'create'])
            ->defaults('role', $role)
            ->name("dashboard.{$plural}.create");
        Route::post($plural, [AcademyAccountController::class, 'store'])
            ->defaults('role', $role)
            ->name("dashboard.{$plural}.store");
        Route::get("{$plural}/{user}", [AcademyAccountController::class, 'show'])
            ->defaults('role', $role)
            ->name("dashboard.{$plural}.show");
        Route::get("{$plural}/{user}/edit", [AcademyAccountController::class, 'edit'])
            ->defaults('role', $role)
            ->name("dashboard.{$plural}.edit");
        Route::put("{$plural}/{user}", [AcademyAccountController::class, 'update'])
            ->defaults('role', $role)
            ->name("dashboard.{$plural}.update");
        Route::delete("{$plural}/{user}", [AcademyAccountController::class, 'destroy'])
            ->defaults('role', $role)
            ->name("dashboard.{$plural}.destroy");
    }
    Route::prefix('dashboard')->group(function () {
    Route::get('academy/ratings', [AcademyRatingController::class, 'index'])
        ->name('dashboard.academy.ratings.index');
    Route::get('academy/ratings/{rating}', [AcademyRatingController::class, 'show'])
        ->name('dashboard.academy.ratings.show');
    Route::get('academy/settings', [AcademySettingsController::class, 'edit'])
        ->name('dashboard.academy.settings.edit');
    Route::put('academy/settings', [AcademySettingsController::class, 'update'])
        ->name('dashboard.academy.settings.update');
    Route::get('academy/profits', [AcademyProfitController::class, 'index'])
        ->name('dashboard.academy.profits.index');
    Route::get('academy/my-profits', [AcademyProfitController::class, 'myProfits'])
        ->name('dashboard.academy.my-profits');
    Route::get('academy/currency/aed-egp', [CurrencyRateController::class, 'aedToEgp'])
        ->name('dashboard.academy.currency.aed-egp');
    });

    Route::resource('requests', RequestsController::class)->names('dashboard.requests');
    Route::resource('tasks', RequestsController::class)->names('dashboard.tasks');
    Route::resource('technical_support', TechnicalSupportController::class)->names('dashboard.technical_support');
    Route::resource('settings', SettingController::class)->names('dashboard.settings');
    Route::resource('special-request', SpecialRequestController::class)->names('dashboard.special-request');
    Route::delete('special-request/{request}/destroy-special-request', [SpecialRequestController::class, 'destroy'])->name('dashboard.special-request.destroy-special-request');
    Route::resource('withdrawal-requests', WithdrawalRequestsController::class)->names('dashboard.withdrawal-requests');
    Route::resource('services', ServiceController::class)->names('dashboard.services');
    Route::resource('my_services', MyServiceController::class)->names('dashboard.my_services');
    Route::resource('partner_systems', PartnerSystemController::class)->names('dashboard.partner_systems');
    Route::resource('salaries', SalaryController::class)->names('dashboard.salaries');
    Route::resource('dashboard/admin-remarks', AdminRemarkController::class)->names('dashboard.admin_remarks');
    Route::resource('adjustments', AdjustmentController::class)->names('dashboard.adjustments');
    Route::resource('holidays', HolidayController::class)->names('dashboard.holidays');
    Route::resource('dashboard/independent-partners', IndependentPartnerController::class)
        ->names('dashboard.independent-partners');
});
// Legacy path (pre domain-split fix): /academy/settings → cockpit settings.
Route::middleware('auth')->get('/academy/settings', function () {
    return redirect()->route('dashboard.academy.settings.edit', absolute: true);
});

Route::get('/dashboard/salaries/fetch-attendance/{user_id}', [SalaryController::class, 'fetchAttendance'])->name('salaries.fetchAttendance');
Route::get('/dashboard/salaries/fetch-adjustments/{user_id}', [SalaryController::class, 'fetchAdjustments'])->name('dashboard.salaries.fetchAdjustments');
// Register Partner
Route::get('/register/partner', [PartnerRegistrationController::class, 'create'])->name('register.partner');
Route::post('/register/partner', [PartnerRegistrationController::class, 'store'])->name('register.partner.store');

// Partner
Route::middleware(['auth', 'role:partner'])->prefix('partner')->group(function () {
    Route::get('/projects/new', [PartnerController::class, 'newProjects'])->name('partner.projects.new');
    Route::get('/quotes', [PartnerController::class, 'quotes'])->name('partner.quotes.index');
    Route::get('/tasks/{project_id}', [PartnerController::class, 'tasks'])->name('partner.tasks');
});

Route::middleware('auth')->group(function () {
    // Meetings
    Route::post('/meetings/store', [MeetingController::class, 'store'])->name('meetings.store');
    Route::put('/meetings/{meeting}', [MeetingController::class, 'update'])->name('meetings.update');
    Route::delete('/meetings/{meeting}', [MeetingController::class, 'destroy'])->name('meetings.destroy');

    // Project Files
    Route::post('/project-files/store', [ProjectFileController::class, 'store'])->name('files.store');
    Route::put('/project-files/{file}', [ProjectFileController::class, 'update'])->name('files.update');
    Route::delete('/project-files/{file}', [ProjectFileController::class, 'destroy'])->name('files.destroy');

    // Project Approvals
    Route::post('/project-approvals/store', [\App\Http\Controllers\ProjectApprovalController::class, 'store'])->name('approvals.store');
    Route::put('/project-approvals/{approval}', [\App\Http\Controllers\ProjectApprovalController::class, 'update'])->name('approvals.update');
    Route::post('/project-approvals/{approval}/approve', [\App\Http\Controllers\ProjectApprovalController::class, 'approve'])->name('approvals.approve');
    Route::delete('/project-approvals/{approval}', [\App\Http\Controllers\ProjectApprovalController::class, 'destroy'])->name('approvals.destroy');

    // Issues
    Route::post('/issues/store', [IssueController::class, 'store'])->name('issues.store');
    Route::post('/issues-request/store', [IssueController::class, 'storeRequest'])->name('issues-request.store');

    Route::patch('/issues/{issue}/status', [IssueController::class, 'updateStatus'])->name('issues.update-status');
    Route::put('/issues/{issue}', [IssueController::class, 'update'])->name('issues.update');
    Route::delete('/issues/{issue}', [IssueController::class, 'destroy'])->name('issues.destroy');

    // Expenses
    Route::resource('expenses', ExpensesController::class);
    Route::resource('expensesRequests', ExpensesRequestController::class);

    // Special Requesst
    Route::post('/special-request/{specialRequest}/update-project-status', [SpecialRequestController::class, 'updateProjectStatus'])
        ->name('dashboard.special-request.update-project-status');

    Route::patch('/special-request/{specialRequest}/update-title', [SpecialRequestController::class, 'updateTitle'])
        ->name('dashboard.special-request.update-title');

    Route::post('/request/{specialRequest}/update-project-status', [SpecialRequestController::class, 'updateRequestStatus'])
        ->name('dashboard.request.update-project-status');
    
    // Tasks
    Route::post('/tasks/store', [TaskController::class, 'store'])->name('tasks.store');
    Route::post('/tasks/request-store', [TaskController::class, 'requestStore'])->name('tasks.request-store');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::post('/tasks/{task}/start-timer', [TaskController::class, 'startTimer'])->name('tasks.start-timer');
    Route::post('/tasks/{task}/pause-timer', [TaskController::class, 'pauseTimer'])->name('tasks.pause-timer');
    Route::post('/tasks/{task}/finish-timer', [TaskController::class, 'finishTimer'])->name('tasks.finish-timer');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Special Request
    Route::post('/special-requests/{specialRequest}/add-stage', [SpecialRequestController::class, 'addStage'])
        ->name('dashboard.special-request.add-stage');
    Route::put('/dashboard/special-request/stages/{stage}', [SpecialRequestController::class, 'updateRequestStage'])
        ->name('dashboard.request.update-stage');
    // Stages
    Route::put('/dashboard/stages/{stage}', [SpecialRequestController::class, 'updateStage'])
        ->name('dashboard.special-request.update-stage');

    Route::delete('/stages/{stage}', [SpecialRequestController::class, 'destroyStage'])
        ->name('dashboard.special-request.destroy-stage');

    Route::delete('/request-stages/{stage}', [SpecialRequestController::class, 'destroyRequestStage'])
        ->name('dashboard.request.destroy-stage');
        
    // Budgets
    Route::post('/special-request/{specialRequest}/update-budget', [SpecialRequestController::class, 'updateProjectBudget'])
        ->name('dashboard.special-request.update-budget');
    Route::post('/payments/{payment}/upload-proof', [SpecialRequestController::class, 'uploadPaymentProof'])
        ->name('dashboard.payments.upload-proof');
    Route::post('/payments/{payment}/confirm', [SpecialRequestController::class, 'confirmPayment'])
        ->name('dashboard.payments.confirm');
    Route::post('/payments/{payment}/reject', [SpecialRequestController::class, 'rejectPayment'])
        ->name('dashboard.payments.reject');

    // Notes
    Route::post('/special-request/{specialRequest}/request-add-notes', [SpecialRequestController::class, 'requestAddNote'])
        ->name('dashboard.special-request.request-add-note');
    Route::delete('/notes/{note}', [SpecialRequestController::class, 'requestDestroyNote'])
        ->name('dashboard.special-request.destroy-note');
    Route::put('/notes/{note}', [SpecialRequestController::class, 'requestUpdateNote'])
        ->name('dashboard.special-request.update-note');


    Route::put('/notes/{note}', [SpecialRequestController::class, 'updateNote'])
        ->name('dashboard.special-request.update-note');
    Route::delete('/notes/{note}', [SpecialRequestController::class, 'destroyNote'])
        ->name('dashboard.special-request.destroy-note');
    Route::post('/notes/{note}/toggle-visibility', [SpecialRequestController::class, 'toggleNoteVisibility'])
        ->name('dashboard.special-request.toggle-note-visibility');

    // تحديث مدة الصيانة
    Route::patch('special-request/{specialRequest}/maintenance', [SpecialRequestController::class, 'updateMaintenance'])->name('dashboard.special-request.update-maintenance');

    // إدارة العملاء المتعددين
    Route::post('special-request/{specialRequest}/add-client', [SpecialRequestController::class, 'addClient'])->name('dashboard.special-request.add-client');
    Route::delete('special-request/{specialRequest}/client/{user}', [SpecialRequestController::class, 'removeClient'])->name('dashboard.special-request.remove-client');

    Route::post('requests/{request}/add-client', [SpecialRequestController::class, 'addRequestClient'])->name('dashboard.request.add-client');
    Route::delete('requests/{request}/client/{user}', [SpecialRequestController::class, 'removeRequestClient'])->name('dashboard.request.remove-client');

    Route::post('special-request/{specialRequest}/assign-partners', [SpecialRequestController::class, 'assignPartners'])->name('dashboard.special-request.assign-partners');
    
    Route::post('special-request/{specialRequest}/request-assign-partners', [SpecialRequestController::class, 'requestAssignPartners'])->name('dashboard.special-request.request-assign-partners');

    Route::delete('special-request/{specialRequest}/partner/{partner}', [SpecialRequestController::class, 'removePartner'])
    ->name('dashboard.special-request.remove-partner');

    // تأكد أن الاسم هنا {partner} وليس {partner_id} أو أي اسم آخر
    Route::delete('/requests/{specialRequest}/partners/{partner}', [SpecialRequestController::class, 'removePartnerRequest'])
        ->name('dashboard.request.remove-partner');

    Route::get('performance', PerformanceController::class)->name('dashboard.performance.show');
    Route::get('requests/{request}/invoice', [RequestsController::class, 'invoice'])->name('dashboard.requests.invoice');
    Route::get('requests/{request}/special-invoice', [RequestsController::class, 'specialInvoice'])
    ->name('dashboard.requests.special-invoice');
    Route::patch('requests/{userRequest}/updateStatus', [RequestsController::class, 'updateStatus'])->name('dashboard.requests.updateStatus');
    Route::post('requests/{userRequest}/rating', [RatingController::class, 'store'])->name('dashboard.requests.rating.store');
});

Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::get('/support/{id}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{id}/message', [SupportController::class, 'sendMessage'])->name('support.message');
    Route::patch('/support/{id}/status', [SupportController::class, 'updateStatus'])->name('support.status');

    // External messages API proxy
    Route::get('/api-messages', [SupportController::class, 'apiMessages'])->name('support.api.messages');
    Route::delete('/api-messages/all', [SupportController::class, 'apiDeleteAllMessages'])->name('support.api.delete-all');
    Route::delete('/api-messages/{id}', [SupportController::class, 'apiDeleteMessage'])->name('support.api.delete');
    Route::post('/api-messages/{id}/reply', [SupportController::class, 'apiReplyMessage'])->name('support.api.reply');

    // Notifications
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});
