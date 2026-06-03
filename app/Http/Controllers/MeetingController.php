<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Services\ProjectActivityLogger;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function store(Request $request)
    {
        $meeting = Meeting::create([
            'special_request_id' => $request->special_request_id,
            'created_by' => auth()->id(),
            'title' => $request->title,
            'meeting_link' => $request->meeting_link,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
        ]);

        if ($request->has('attendees')) {
            $meeting->participants()->attach($request->attendees);
        }

        if ($request->special_request_id) {
            app(ProjectActivityLogger::class)->logSpecialRequest(
                (int) $request->special_request_id,
                'تم جدولة اجتماع: «'.$request->title.'»',
                'meeting',
            );
        }

        return back()->with('success', 'تم جدولة الاجتماع وإرسال الدعوات');
    }

    public function update(Request $request, Meeting $meeting)
    {
        $messages = [
            'title.required' => 'يرجى إدخال عنوان الاجتماع.',
            'start_at.required' => 'يجب تحديد وقت بداية الاجتماع.',
            'end_at.required' => 'يجب تحديد وقت نهاية الاجتماع.',
            'end_at.after' => 'خطأ: يجب أن يكون وقت الانتهاء بعد وقت البدء.',
            'meeting_link.url' => 'رابط الاجتماع يجب أن يكون رابطاً صحيحاً.',
        ];

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'attendees' => 'required|array',
            'meeting_link' => 'nullable|url',
            'start_at' => 'required',
            'end_at' => 'required|after:start_at',
        ], $messages);

        $meeting->update($data);

        if ($meeting->special_request_id) {
            app(ProjectActivityLogger::class)->logSpecialRequest(
                $meeting->special_request_id,
                'تم تعديل اجتماع: «'.$meeting->title.'»',
                'meeting',
            );
        }

        return back()->with('success', 'تم تحديث الاجتماع بنجاح');
    }

    public function destroy(Meeting $meeting)
    {
        $title = $meeting->title;
        $projectId = $meeting->special_request_id;

        $meeting->delete();

        if ($projectId) {
            app(ProjectActivityLogger::class)->logSpecialRequest(
                $projectId,
                'تم حذف اجتماع: «'.$title.'»',
                'meeting',
            );
        }

        return back()->with('success', 'تم حذف الاجتماع');
    }
}
