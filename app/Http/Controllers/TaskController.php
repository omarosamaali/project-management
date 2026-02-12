<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
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
        $task->update($validated);
        return redirect()->back()->with('success', 'تم تعديل المهمة بنجاح');
    }

    // حذف المهمة
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'تم حذف المهمة بنجاح');
    }
}
