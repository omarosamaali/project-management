<?php

use App\Http\Controllers\Dashboard\SystemController;
use App\Http\Controllers\Dashboard\PartnerController;
use App\Http\Controllers\Dashboard\ClientController;
use App\Http\Controllers\Dashboard\RequestsController;
use App\Http\Controllers\Dashboard\SupportController;
use App\Http\Controllers\Dashboard\TechnicalSupportController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\RatingController;
use App\Http\Controllers\Dashboard\PerformanceController;
use App\Http\Controllers\Dashboard\WithdrawalRequestsController;
use App\Http\Controllers\Dashboard\SpecialRequestController;
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
use App\Http\Controllers\RequestFileController;
use App\Http\Controllers\PartnerSystemController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\AdminRemarkController;
use App\Http\Controllers\CreateRequestController;
use App\Http\Controllers\Dashboard\AdjustmentController;
use App\Http\Controllers\Dashboard\IndependentPartnerController;
use App\Http\Controllers\Dashboard\MyStoreController;
use App\Http\Controllers\ProjectMeetingController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ZiinaPaymentController;
use App\Http\Controllers\MyCoursesController;
use App\Http\Controllers\EducationalResourceController;
use App\Models\MyStore;

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
    Route::resource('work-times', WorkTimeController::class);
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

Route::name('dashboard.')->prefix('dashboard')->group(function () {
    Route::resource('courses', CourseController::class);
});
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
        
    Route::get('/courses/{course}', [CourseController::class, 'userShow'])->name('courses.show');
    Route::resource('partners', PartnerController::class)->names('dashboard.partners');
    Route::resource('clients', ClientController::class)->names('dashboard.clients');
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
    Route::resource('dashboard/independent-partners', IndependentPartnerController::class)
        ->names('dashboard.independent-partners');
});
Route::get('/dashboard/salaries/fetch-attendance/{user_id}', [SalaryController::class, 'fetchAttendance'])->name('salaries.fetchAttendance');
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

    Route::post('/request/{specialRequest}/update-project-status', [SpecialRequestController::class, 'updateRequestStatus'])
        ->name('dashboard.request.update-project-status');
    
    // Tasks
    Route::post('/tasks/store', [TaskController::class, 'store'])->name('tasks.store');
    Route::post('/tasks/request-store', [TaskController::class, 'requestStore'])->name('tasks.request-store');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
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
});
