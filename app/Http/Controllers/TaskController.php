<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\SpecialRequest;
use App\Models\Requests as ProjectRequest;
use App\Services\ProjectActivityLogger;
use App\Support\TaskPermissions;
use Carbon\Carbon;

class TaskController extends Controller
{
    private function accumulateRunningTime(Task $task): void
    {
        if (!$task->is_timer_running || !$task->timer_started_at) {
            return;
        }

        $startedAt = Carbon::parse($task->timer_started_at);
        $seconds = max(0, $startedAt->diffInSeconds(now()));

        $task->tracked_seconds = (int) ($task->tracked_seconds ?? 0) + $seconds;
        $task->timer_started_at = null;
        $task->is_timer_running = false;
        $task->save();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'special_request_id' => 'required|exists:special_requests,id',
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'project_stage_id' => 'nullable|exists:project_stages,id',
            'status' => 'required'
        ]);

        $project = SpecialRequest::findOrFail($request->special_request_id);
        TaskPermissions::authorizeManageProject(auth()->user(), $project);

        Task::create($validated);

        app(ProjectActivityLogger::class)->logSpecialRequest(
            (int) $request->special_request_id,
            'تم إضافة مهمة جديدة: «'.$validated['title'].'»',
            'task',
        );

        if ((int) $validated['user_id'] !== (int) auth()->id()) {
            $project = SpecialRequest::find($request->special_request_id);
            \App\Models\AppNotification::notify(
                (int) $validated['user_id'],
                $project?->title ?? 'مشروع',
                'تم إسناد مهمة جديدة إليك: «'.$validated['title'].'»',
                route('dashboard.special-request.show', $request->special_request_id),
                'fa-tasks',
                'info',
            );
        }

        return redirect()->back()->with('success', 'تم إضافة المهمة بنجاح');
    }

    public function requestStore(Request $request)
    {
        // تنظيف المدخلات
        $input = array_filter($request->all(), function ($value) {
            return !is_null($value) && $value !== '';
        });

        // التحقق من البيانات
        $validated = \Validator::make($input, [
            'special_request_id' => 'nullable|exists:special_requests,id',
            'request_id'         => 'nullable|exists:requests,id',
            'user_id'            => 'required|exists:users,id',
            'title'              => 'required|string|max:255',
            'details'            => 'nullable|string',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'project_stage_id'   => 'nullable|exists:project_stages,id',
            'request_stage_id'   => 'nullable|exists:request_stages,id', // ✅
            'status'             => 'required|string'
        ], [
            'request_id.exists'         => 'الطلب المحدد غير موجود',
            'user_id.exists'            => 'المستخدم المحدد غير موجود',
            'project_stage_id.exists'   => 'مرحلة المشروع غير موجودة',
            'request_stage_id.exists'   => 'مرحلة الطلب غير موجودة',
        ])->validate();

        if (!empty($validated['special_request_id'])) {
            TaskPermissions::authorizeManageProject(
                auth()->user(),
                SpecialRequest::findOrFail($validated['special_request_id'])
            );
        } elseif (!empty($validated['request_id'])) {
            TaskPermissions::authorizeManageProject(
                auth()->user(),
                ProjectRequest::findOrFail($validated['request_id'])
            );
        }

        $task = Task::create($validated);

        $logger = app(ProjectActivityLogger::class);
        $description = 'تم إضافة مهمة جديدة: «'.$task->title.'»';

        if (!empty($validated['special_request_id'])) {
            $logger->logSpecialRequest((int) $validated['special_request_id'], $description, 'task');
        } elseif (!empty($validated['request_id'])) {
            $logger->logRequest((int) $validated['request_id'], $description, 'task');
        }

        return redirect()->back()->with('success', 'تم إضافة المهمة بنجاح');
    }

    public function edit(Task $task)
    {
        TaskPermissions::authorizeManage(auth()->user(), $task);

        $task->load(['user', 'stage']);

        return response()->json([
            'id'               => $task->id,
            'title'            => $task->title,
            'details'          => $task->details,
            'user_id'          => $task->user_id,
            'user_name'        => $task->user->name ?? 'غير محدد', // إضافة الاسم هنا
            'project_stage_id' => $task->project_stage_id,
            'stage_title'      => $task->stage->title ?? 'مهمة عامة', // إضافة عنوان المرحلة هنا
            'status'           => $task->status,
            'special_request_id' => $task->special_request_id,
            'start_date'       => $task->start_date ? date('Y-m-d', strtotime($task->start_date)) : null,
            'end_date'         => $task->end_date   ? date('Y-m-d', strtotime($task->end_date))   : null,
            'tracked_seconds'  => $task->elapsed_tracked_seconds,
            'is_timer_running' => (bool) $task->is_timer_running,
        ]);
    }
    // تحديث المهمة
    public function update(Request $request, Task $task)
    {
        TaskPermissions::authorizeManage(auth()->user(), $task);

        $rules = [
            'user_id'            => 'required|exists:users,id',
            'title'              => 'required|string|max:255',
            'details'            => 'nullable|string',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'project_stage_id'   => 'nullable|exists:project_stages,id',
            'request_stage_id'   => 'nullable|exists:request_stages,id',
        ];

        if (auth()->user()->role === 'admin') {
            $rules['status'] = 'required|string';
        }

        $validated = $request->validate($rules);

        if (auth()->user()->role !== 'admin') {
            $validated['status'] = $task->status;
        }

        $oldStatus = $task->status;
        $task->update($validated);

        $logger = app(ProjectActivityLogger::class);
        if (!$logger->isTaskCompleted($oldStatus) && $logger->isTaskCompleted($validated['status'])) {
            $logger->logTaskCompleted($task->fresh(), auth()->id());
        } else {
            $statusChanged = $oldStatus !== $validated['status'] ? " (الحالة: {$oldStatus} → {$validated['status']})" : '';
            $description = 'تم تعديل المهمة: «'.$task->title.'»'.$statusChanged;
            if ($task->special_request_id) {
                $logger->logSpecialRequest($task->special_request_id, $description, 'task');
            } elseif ($task->request_id) {
                $logger->logRequest($task->request_id, $description, 'task');
            }
        }

        return redirect()->back()->with('success', 'تم تعديل المهمة بنجاح');
    }

    public function startTimer(Task $task)
    {
        TaskPermissions::authorizeTrack(auth()->user(), $task);

        if (!$task->is_timer_running) {
            $task->timer_started_at = now();
            $task->is_timer_running = true;

            // عند البدء الفعلي نضعها قيد الإنجاز إذا لم تكن منتهية
            if ($task->status !== 'منتهية') {
                $task->status = 'قيد الإنجاز';
            }

            $task->save();
        }

        return redirect()->back()->with('success', 'تم بدء العمل على المهمة');
    }

    public function pauseTimer(Task $task)
    {
        TaskPermissions::authorizeTrack(auth()->user(), $task);

        $this->accumulateRunningTime($task);

        if ($task->status !== 'منتهية') {
            $task->status = 'بالانتظار';
            $task->save();
        }

        return redirect()->back()->with('success', 'تم إيقاف العداد مؤقتاً');
    }

    public function finishTimer(Task $task)
    {
        TaskPermissions::authorizeTrack(auth()->user(), $task);

        $wasCompleted = app(ProjectActivityLogger::class)->isTaskCompleted($task->status);

        $this->accumulateRunningTime($task);
        $task->status = 'منتهية';
        $task->save();

        if (!$wasCompleted) {
            app(ProjectActivityLogger::class)->logTaskCompleted($task->fresh(), auth()->id());
        }

        return redirect()->back()->with('success', 'تم إنهاء المهمة وتثبيت الوقت');
    }

    // حذف المهمة
    public function destroy(Task $task)
    {
        TaskPermissions::authorizeManage(auth()->user(), $task);

        $title = $task->title;
        $specialId = $task->special_request_id;
        $requestId = $task->request_id;

        $task->delete();

        $logger = app(ProjectActivityLogger::class);
        $description = 'تم حذف المهمة: «'.$title.'»';
        if ($specialId) {
            $logger->logSpecialRequest($specialId, $description, 'task');
        } elseif ($requestId) {
            $logger->logRequest($requestId, $description, 'task');
        }

        return redirect()->back()->with('success', 'تم حذف المهمة بنجاح');
    }
}
