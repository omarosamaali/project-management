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
            'start_at' => 'required|string',
            'end_at' => 'required|string',
            'meeting_link' => 'nullable|url',
            'meeting_type' => 'nullable|in:online,in_person',
            'location' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:50',
            'attendees' => 'nullable|array'
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

        $timezone = $validated['timezone'] ?? 'Asia/Dubai';
        $meeting = ProjectMeeting::create([
            'special_request_id' => $request->special_request_id,
            'request_id' => $request->request_id,
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'meeting_link' => $validated['meeting_link'] ?? null,
            'meeting_type' => $validated['meeting_type'] ?? 'online',
            'location' => $validated['location'] ?? null,
            'timezone' => $timezone,
            'start_at' => \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['start_at'], $timezone),
            'end_at' => \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['end_at'], $timezone),
        ]);

        $creatorId = (int) auth()->id();
        $attendees = collect($validated['attendees'])->map('intval')->filter(fn($id) => $id > 0)->unique();
        if ($creatorId > 0 && !$attendees->contains($creatorId)) {
            $attendees->push($creatorId);
        }
        $attendeesData = $attendees->mapWithKeys(fn($id) => [
            $id => ['status' => $id === $creatorId ? 'accepted' : 'pending']
        ])->all();
        $meeting->participants()->attach($attendeesData);

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
            'meeting_type' => 'nullable|in:online,in_person',
            'location' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:50',
            'start_at' => 'required|string',
            'end_at' => 'required|string',
            'attendees' => 'nullable|array',
        ]);

        $timezone = $validated['timezone'] ?? $meeting->getMeetingTimezone();

        $meeting->update([
            'title'        => $validated['title'],
            'meeting_link' => $validated['meeting_link'] ?? null,
            'meeting_type' => $validated['meeting_type'] ?? $meeting->meeting_type,
            'location'     => $validated['location'] ?? null,
            'timezone'     => $timezone,
            'start_at'     => \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['start_at'], $timezone),
            'end_at'       => \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['end_at'], $timezone),
        ]);

        $creatorId = (int) $meeting->created_by;
        $attendees = collect($request->input('attendees', []))->map('intval')->filter(fn($id) => $id > 0)->unique();
        if ($creatorId > 0 && !$attendees->contains($creatorId)) {
            $attendees->push($creatorId);
        }
        if ($attendees->isNotEmpty()) {
            $existingStatuses = $meeting->participants->mapWithKeys(fn($p) => [$p->id => $p->pivot->status])->toArray();
            $syncData = $attendees->mapWithKeys(fn($id) => [
                $id => ['status' => $existingStatuses[$id] ?? 'pending']
            ])->all();
            $meeting->participants()->sync($syncData);
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
