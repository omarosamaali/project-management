<?php

namespace App\Http\Controllers;

use App\Models\ProjectMeeting;
use Illuminate\Http\Request;
use App\Models\ProjectProposal;

class ProjectMeetingController extends Controller
{
    // حفظ اجتماع جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'special_request_id' => 'required',
            'title' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'meeting_link' => 'nullable|url',
            'attendees' => 'required|array' // تم تغييرها من user_ids إلى attendees
        ]);

        $meeting = ProjectMeeting::create([
            'special_request_id' => $validated['special_request_id'],
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'meeting_link' => $validated['meeting_link'] ?? null,
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
        ]);

        // إضافة المشاركين باستخدام الاسم الصحيح
        $meeting->participants()->attach($validated['attendees'], ['status' => 'pending']);

        return back()->with('success', 'تم جدولة الاجتماع بنجاح');
    }

    // يجب أن يكون الاسم هنا أيضاً $meeting ليطابق الراوت
    public function destroy(ProjectMeeting $meeting)
    {
        $meeting->delete();
        return back()->with('success', 'تم الحذف');
    }

    // 2. ميثود تحديث البيانات (للتعديل)
    public function update(Request $request, ProjectMeeting $meeting)
    {
        if ($meeting->created_by !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'meeting_link' => 'nullable|url',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'attendees' => 'required|array',
        ]);

        $meeting->update($validated);

        // تحديث قائمة الحضور في الجدول الوسيط
        if ($request->has('attendees')) {
            $meeting->participants()->sync($request->attendees);
        }

        return back()->with('success', 'تم تحديث بيانات الاجتماع.');
    }

    // 3. ميثود تحديث الحالة (موافق/يعتذر/حضر)
    public function updateStatus(Request $request, ProjectMeeting $meeting)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,declined,attended,absent'
        ]);

        // تحديث حالة المستخدم الحالي فقط
        $meeting->participants()->updateExistingPivot(auth()->id(), [
            'status' => $validated['status']
        ]);

        return back()->with('success', 'تم تحديث الحالة بنجاح.');
    }

    public function accept($id)
    {
        $proposal = ProjectProposal::findOrFail($id);
        ProjectProposal::where('project_id', $proposal->project_id)->update(['status' => 'rejected']);
        $proposal->update(['status' => 'accepted']);
        return back()->with('success', 'تم قبول العرض بنجاح');
    }

    public function reject($id)
    {
        ProjectProposal::findOrFail($id)->update(['status' => 'rejected']);
        return back()->with('success', 'تم استبعاد العرض');
    }
}
