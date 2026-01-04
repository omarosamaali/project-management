<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\ProjectActivity;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'start_at' => 'required',
            'end_at' => 'required',
        ]);

        // 1. جلب المختارين من الـ Checkboxes (إذا وجدوا)
        $attendees = $request->input('attendees', []);

        // 2. معالجة الأسماء المكتوبة يدوياً (إذا وجدت)
        if ($request->manual_attendees) {
            $manual = explode(',', $request->manual_attendees);
            // دمج المصفوفات وتنظيف الفراغات
            $attendees = array_merge($attendees, array_map('trim', $manual));
        }

        // 3. حذف القيم المكررة أو الفارغة
        $attendees = array_unique(array_filter($attendees));

        // 4. الحفظ
        Meeting::create([
            'special_request_id' => $request->special_request_id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'attendees' => $attendees, // ستتحول لـ JSON تلقائياً بفضل الـ Cast
            'meeting_link' => $request->meeting_link,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
        ]);

        return back()->with('success', 'تم جدولة الاجتماع بنجاح');
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
        ], $messages); // تمرير الرسائل هنا

        $meeting->update($data);

        return back()->with('success', 'تم تحديث الاجتماع بنجاح');
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();
        return back()->with('success', 'تم حذف الاجتماع');
    }
}
