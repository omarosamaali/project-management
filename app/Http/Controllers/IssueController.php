<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\SpecialRequest;
use App\Models\Requests as ProjectRequest;
use App\Services\WhatsAppOTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IssueController extends Controller {
    public function store(Request $request)
    {
        $data = $request->validate([
            'special_request_id' => 'required|exists:special_requests,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_users' => 'nullable|array',
            'image' => 'nullable|image'
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('issues', 'public');
        }
        $data['user_id'] = auth()->id();
        $data['status'] = 'new';
        Issue::create($data);
        \App\Models\ProjectActivity::create([
            'special_request_id' => $request->special_request_id,
            'user_id' => auth()->id(),
            'type' => 'file',
            'description' => 'تم تسجيل مشكلة جديدة: ' . $request->title,
        ]);
        $this->notifyMembers('special_request_id', $request->special_request_id, "تم تسجيل خطأ/معوق جديد: ({$request->title})");
        return back()->with('success', 'تم تسجيل المشكلة بنجاح');
    }
    public function storeRequest(Request $request)
    {
        // تغيير اسم الحقل هنا ليتطابق مع الفورم
        $data = $request->validate([
            'request_id' => 'required|exists:requests,id', // تأكد من اسم الجدول
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_users' => 'nullable|array',
            'image' => 'nullable|image'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('issues', 'public');
        }

        $data['user_id'] = auth()->id();
        $data['status'] = 'new';

        // ملاحظة: تأكد أن الموديل Issue يحتوي على حقل special_request_id في الـ fillable
        Issue::create($data);

        \App\Models\ProjectActivity::create([
            'request_id' => $request->request_id,
            'user_id' => auth()->id(),
            'type' => 'file',
            'description' => 'تم تسجيل مشكلة جديدة: ' . $request->title,
        ]);
        $this->notifyMembers('request_id', $request->request_id, "تم تسجيل خطأ/معوق جديد: ({$request->title})");
        return back()->with('success', 'تم تسجيل المشكلة بنجاح');
    }

    public function updateStatus(Request $request, Issue $issue)
    {
        // يمكن لأي شخص التفاعل، أو تخصيصها للأدمن فقط
        $issue->update(['status' => $request->status]);
        return back()->with('success', 'تم تحديث حالة المشكلة');
    }

    // دالة التحديث
    public function update(Request $request, \App\Models\Issue $issue)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_users' => 'required|array',
        ]);

        $issue->update($data);

        return back()->with('success', 'تم تحديث البيانات بنجاح');
    }

    private function notifyMembers(string $field, $id, string $eventText): void
    {
        try {
            $whatsapp = app(WhatsAppOTPService::class);
            if ($field === 'special_request_id') {
                $project = SpecialRequest::find($id);
                $members = $project?->partners()->get() ?? collect();
                $title = $project?->title ?? '';
            } else {
                $project = ProjectRequest::find($id);
                $members = $project?->partners()->get() ?? collect();
                $title = $project?->title ?? "طلب #{$id}";
            }
            foreach ($members as $member) {
                if ($member->phone) {
                    $whatsapp->sendProjectNotification($member->phone, $member->name, $eventText, $title);
                }
            }
        } catch (\Exception $e) {
            \Log::error("[NOTIFY] {$eventText}: " . $e->getMessage());
        }
    }

    // دالة الحذف
    public function destroy(Issue $issue)
    {
        if ($issue->image && \Storage::disk('public')->exists($issue->image)) {
            \Storage::disk('public')->delete($issue->image);
        }
        $issue->delete();
        return back()->with('success', 'تم حذف السجل بنجاح');
    }
}