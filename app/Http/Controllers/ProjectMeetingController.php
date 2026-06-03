<?php

namespace App\Http\Controllers;

use App\Models\ProjectMeeting;
use App\Models\SpecialRequest;
use App\Models\Requests as ProjectRequest;
use App\Services\ProjectActivityLogger;
use Illuminate\Http\Request;
use App\Models\ProjectProposal;

class ProjectMeetingController extends Controller
{
    // حفظ اجتماع جديد
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'meeting_link' => 'nullable|url',
            'attendees' => 'required|array'
        ];

        // إضافة validation فقط لو الحقل موجود ومش فاضي
        if ($request->filled('special_request_id')) {
            $rules['special_request_id'] = 'exists:special_requests,id';
        }

        if ($request->filled('request_id')) {
            $rules['request_id'] = 'exists:requests,id';
        }

        $validated = $request->validate($rules);
        $validated['attendees'] = $this->mergeProjectClientAttendees($request, $validated['attendees']);

        $meeting = ProjectMeeting::create([
            'special_request_id' => $request->special_request_id,
            'request_id' => $request->request_id,
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'meeting_link' => $validated['meeting_link'] ?? null,
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
        ]);

        $meeting->participants()->attach($validated['attendees'], ['status' => 'pending']);

        $description = 'تم جدولة اجتماع جديد: «'.$validated['title'].'»';
        $logger = app(ProjectActivityLogger::class);
        if ($request->filled('special_request_id')) {
            $logger->logSpecialRequest((int) $request->special_request_id, $description, 'meeting');
        } elseif ($request->filled('request_id')) {
            $logger->logRequest((int) $request->request_id, $description, 'meeting');
        }

        return back()->with('success', 'تم جدولة الاجتماع بنجاح');
    }
    // يجب أن يكون الاسم هنا أيضاً $meeting ليطابق الراوت
    public function destroy(ProjectMeeting $meeting)
    {
        $title = $meeting->title;
        $specialId = $meeting->special_request_id;
        $requestId = $meeting->request_id;

        $meeting->delete();

        $description = 'تم حذف اجتماع: «'.$title.'»';
        $logger = app(ProjectActivityLogger::class);
        if ($specialId) {
            $logger->logSpecialRequest($specialId, $description, 'meeting');
        } elseif ($requestId) {
            $logger->logRequest($requestId, $description, 'meeting');
        }

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

        $description = 'تم تعديل اجتماع: «'.$meeting->title.'»';
        $logger = app(ProjectActivityLogger::class);
        if ($meeting->special_request_id) {
            $logger->logSpecialRequest($meeting->special_request_id, $description, 'meeting');
        } elseif ($meeting->request_id) {
            $logger->logRequest($meeting->request_id, $description, 'meeting');
        }

        return back()->with('success', 'تم تحديث بيانات الاجتماع.');
    }

    // 3. ميثود تحديث الحالة (موافق/يعتذر/حضر)
    public function updateStatus(Request $request, ProjectMeeting $meeting)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,declined,attended,absent'
        ]);

        $userId = auth()->id();

        if (!$meeting->participants()->where('users.id', $userId)->exists()) {
            if (!$this->userCanRespondToMeeting($meeting, $userId)) {
                abort(403, 'غير مصرح لك بالرد على هذا الاجتماع.');
            }
            $meeting->participants()->attach($userId, ['status' => $validated['status']]);
        } else {
            $meeting->participants()->updateExistingPivot($userId, [
                'status' => $validated['status'],
            ]);
        }

        return back()->with('success', 'تم تحديث الحالة بنجاح.');
    }

    private function mergeProjectClientAttendees(Request $request, array $attendees): array
    {
        $project = null;

        if ($request->filled('special_request_id')) {
            $project = SpecialRequest::find($request->special_request_id);
        } elseif ($request->filled('request_id')) {
            $project = ProjectRequest::find($request->request_id);
        }

        if (!$project) {
            return $attendees;
        }

        $clientIds = $project->allProjectClients()->pluck('id')->all();

        return array_values(array_unique(array_merge($attendees, $clientIds)));
    }

    private function userCanRespondToMeeting(ProjectMeeting $meeting, int $userId): bool
    {
        if (auth()->user()->role === 'admin') {
            return true;
        }

        if ($meeting->created_by === $userId) {
            return true;
        }

        if ($meeting->special_request_id) {
            $project = SpecialRequest::find($meeting->special_request_id);

            return $project && $project->isClientMember($userId);
        }

        if ($meeting->request_id) {
            $project = ProjectRequest::find($meeting->request_id);

            return $project && $project->isClientMember($userId);
        }

        return $meeting->participants()->where('users.id', $userId)->exists();
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
