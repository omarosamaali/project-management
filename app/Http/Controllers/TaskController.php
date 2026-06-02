<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\SpecialRequest;
use App\Models\Requests as ProjectRequest;
use App\Services\WhatsAppOTPService;
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
        Task::create($validated);
        \App\Models\ProjectActivity::create([
            'special_request_id' => $request->special_request_id,
            'user_id' => auth()->id(),
            'type' => 'file',
            'description' => 'تم اضافة مهمة جديدة للمشروع',
        ]);

        // إشعار واتساب لأعضاء المشروع
        $specialRequest = SpecialRequest::find($request->special_request_id);
        if ($specialRequest) {
            $whatsapp = app(WhatsAppOTPService::class);
            foreach ($specialRequest->partners()->get() as $member) {
                if ($member->phone) {
                    try {
                        $whatsapp->sendNewTaskNotification(
                            phone: $member->phone,
                            memberName: $member->name,
                            taskTitle: $validated['title'],
                            projectTitle: $specialRequest->title,
                        );
                    } catch (\Exception $e) {
                        \Log::error("[TASK] فشل إرسال إشعار لـ {$member->name}: " . $e->getMessage());
                    }
                }
            }
            try {
                $whatsapp->notifyManager("تم إضافة مهمة جديدة: ({$validated['title']})", $specialRequest->title);
            } catch (\Exception $e) {
                \Log::error("[TASK] فشل إشعار المدير: " . $e->getMessage());
            }
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

        // إنشاء المهمة
        $task = Task::create($validated);

        // تسجيل النشاط
        if (!empty($validated['special_request_id'])) {
            \App\Models\ProjectActivity::create([
                'special_request_id' => $validated['special_request_id'],
                'user_id'            => auth()->id(),
                'type'               => 'task',
                'description'        => 'تم إضافة مهمة جديدة: ' . $task->title,
            ]);
        }

        // إشعار واتساب لأعضاء المشروع أو الطلب
        $whatsapp = app(WhatsAppOTPService::class);

        if (!empty($validated['special_request_id'])) {
            $specialRequest = SpecialRequest::find($validated['special_request_id']);
            if ($specialRequest) {
                $projectTitle = $specialRequest->title;
                $members = $specialRequest->partners()->get();
                foreach ($members as $member) {
                    if ($member->phone) {
                        try {
                            $whatsapp->sendNewTaskNotification(
                                phone: $member->phone,
                                memberName: $member->name,
                                taskTitle: $task->title,
                                projectTitle: $projectTitle,
                            );
                        } catch (\Exception $e) {
                            \Log::error("[TASK] فشل إرسال إشعار لـ {$member->name}: " . $e->getMessage());
                        }
                    }
                }
                try {
                    $whatsapp->notifyManager("تم إضافة مهمة جديدة: ({$task->title})", $projectTitle);
                } catch (\Exception $e) {
                    \Log::error("[TASK] فشل إشعار المدير: " . $e->getMessage());
                }
            }
        } elseif (!empty($validated['request_id'])) {
            $projectRequest = ProjectRequest::find($validated['request_id']);
            if ($projectRequest) {
                $projectTitle = $projectRequest->title ?? "طلب #{$projectRequest->id}";
                $members = $projectRequest->partners()->get();
                foreach ($members as $member) {
                    if ($member->phone) {
                        try {
                            $whatsapp->sendNewTaskNotification(
                                phone: $member->phone,
                                memberName: $member->name,
                                taskTitle: $task->title,
                                projectTitle: $projectTitle,
                            );
                        } catch (\Exception $e) {
                            \Log::error("[TASK] فشل إرسال إشعار لـ {$member->name}: " . $e->getMessage());
                        }
                    }
                }
                try {
                    $whatsapp->notifyManager("تم إضافة مهمة جديدة: ({$task->title})", $projectTitle);
                } catch (\Exception $e) {
                    \Log::error("[TASK] فشل إشعار المدير: " . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('success', 'تم إضافة المهمة بنجاح');
    }

    public function edit(Task $task)
    {
        // تحميل العلاقات لجلب الأسماء
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
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'project_stage_id' => 'nullable|exists:project_stages,id',
            'status' => 'required'
        ]);

        $oldStatus = $task->status;
        $task->update($validated);

        try {
            $whatsapp = app(WhatsAppOTPService::class);
            $project  = $task->specialRequest ?? $task->projectRequest;
            $title    = $project->title ?? "مهمة #{$task->id}";
            $statusChanged = $oldStatus !== $validated['status'] ? " (الحالة: {$oldStatus} ← {$validated['status']})" : '';
            $whatsapp->notifyManager("تم تعديل المهمة: ({$task->title}){$statusChanged}", $title);
        } catch (\Exception $e) {
            \Log::error("[TASK_UPDATE] فشل إشعار المدير: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'تم تعديل المهمة بنجاح');
    }

    public function startTimer(Task $task)
    {
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
        $this->accumulateRunningTime($task);

        if ($task->status !== 'منتهية') {
            $task->status = 'بالانتظار';
            $task->save();
        }

        return redirect()->back()->with('success', 'تم إيقاف العداد مؤقتاً');
    }

    public function finishTimer(Task $task)
    {
        $this->accumulateRunningTime($task);
        $task->status = 'منتهية';
        $task->save();

        return redirect()->back()->with('success', 'تم إنهاء المهمة وتثبيت الوقت');
    }

    // حذف المهمة
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'تم حذف المهمة بنجاح');
    }
}
