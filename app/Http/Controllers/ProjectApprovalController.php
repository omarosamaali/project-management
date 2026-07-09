<?php

namespace App\Http\Controllers;

use App\Models\ProjectApproval;
use App\Services\ProjectActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectApprovalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240',
            'special_request_id' => 'required|exists:special_requests,id',
            'approver_ids' => 'required|array|min:1',
            'approver_ids.*' => 'exists:users,id',
        ]);

        $path = $request->file('file')->store('project_approvals', 'public');

        $approval = ProjectApproval::create([
            'special_request_id' => $request->special_request_id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_type' => $request->file('file')->getClientOriginalExtension(),
            'status' => 'pending',
        ]);

        $approval->approvers()->sync(
            collect($request->approver_ids)->unique()->mapWithKeys(fn ($id) => [$id => ['approved_at' => null]])->all()
        );

        app(ProjectActivityLogger::class)->logSpecialRequest(
            (int) $request->special_request_id,
            'تم إضافة مستند للاعتماد: «'.$request->title.'»',
            'approval',
        );

        return back()->with('success', 'تم إضافة المستند للاعتماد بنجاح');
    }

    public function update(Request $request, ProjectApproval $approval)
    {
        if (!$this->userCanManageApproval($approval)) {
            return back()->with('error', 'لا يمكن تعديل هذا المستند.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'approver_ids' => 'nullable|array|min:1',
            'approver_ids.*' => 'exists:users,id',
        ]);

        $approval->update($request->only('title', 'description'));

        if ($request->has('approver_ids')) {
            $existing = $approval->approvers()->get()->keyBy('id');
            $sync = [];
            foreach (collect($request->approver_ids)->unique() as $userId) {
                $sync[$userId] = [
                    'approved_at' => $existing->get($userId)?->pivot?->approved_at,
                ];
            }
            $approval->approvers()->sync($sync);
            $approval->update(['status' => 'pending']);
            $approval->refreshApprovalStatus();
        }

        return back()->with('success', 'تم تحديث بيانات الاعتماد');
    }

    public function approve(ProjectApproval $approval)
    {
        $userId = auth()->id();
        $pivot = $approval->approvers()->where('users.id', $userId)->first();

        if (!$pivot) {
            return back()->with('error', 'لست ضمن قائمة المعتمدين لهذا المستند.');
        }

        if ($pivot->pivot->approved_at) {
            return back()->with('success', 'لقد اعتمدت هذا المستند مسبقاً.');
        }

        $approval->approvers()->updateExistingPivot($userId, ['approved_at' => now()]);
        $approval->refreshApprovalStatus();

        $logger = app(ProjectActivityLogger::class);
        $logger->logSpecialRequest(
            $approval->special_request_id,
            auth()->user()->name.' اعتمد المستند: «'.$approval->title.'»',
            'approval',
            $userId,
        );

        if ($approval->fresh()->isApproved()) {
            $logger->logSpecialRequest(
                $approval->special_request_id,
                'تم اعتماد المستند بالكامل: «'.$approval->title.'»',
                'approval',
            );
        }

        return back()->with('success', 'تم تسجيل اعتمادك بنجاح');
    }

    public function destroy(ProjectApproval $approval)
    {
        if (!$this->userCanManageApproval($approval)) {
            return back()->with('error', 'لا يمكن حذف هذا المستند.');
        }

        Storage::disk('public')->delete($approval->file_path);
        $approval->approvers()->detach();
        $approval->delete();

        return back()->with('success', 'تم حذف مستند الاعتماد');
    }

    private function userCanManageApproval(ProjectApproval $approval): bool
    {
        return (int) $approval->user_id === (int) auth()->id() && !$approval->isApproved();
    }

}
