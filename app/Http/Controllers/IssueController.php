<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Requests as ProjectRequest;
use App\Services\ProjectActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IssueController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'special_request_id' => 'required|exists:special_requests,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_users' => 'nullable|array',
            'image' => 'nullable|image',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('issues', 'public');
        }
        $data['user_id'] = auth()->id();
        $data['status'] = 'new';
        Issue::create($data);

        app(ProjectActivityLogger::class)->logSpecialRequest(
            (int) $request->special_request_id,
            'تم تسجيل خطأ/معوق جديد: «'.$request->title.'»',
            'issue',
        );

        return back()->with('success', 'تم تسجيل المشكلة بنجاح');
    }

    public function storeRequest(Request $request)
    {
        $data = $request->validate([
            'request_id' => 'required|exists:requests,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_users' => 'nullable|array',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('issues', 'public');
        }

        $data['user_id'] = auth()->id();
        $data['status'] = 'new';

        Issue::create($data);

        app(ProjectActivityLogger::class)->logRequest(
            (int) $request->request_id,
            'تم تسجيل خطأ/معوق جديد: «'.$request->title.'»',
            'issue',
        );

        return back()->with('success', 'تم تسجيل المشكلة بنجاح');
    }

    public function updateStatus(Request $request, Issue $issue)
    {
        $issue->update(['status' => $request->status]);

        $description = 'تم تحديث حالة المعوق «'.$issue->title.'» إلى: '.$request->status;
        if ($issue->special_request_id) {
            app(ProjectActivityLogger::class)->logSpecialRequest($issue->special_request_id, $description, 'issue');
        } elseif ($issue->request_id) {
            app(ProjectActivityLogger::class)->logRequest($issue->request_id, $description, 'issue');
        }

        return back()->with('success', 'تم تحديث حالة المشكلة');
    }

    public function update(Request $request, Issue $issue)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_users' => 'required|array',
        ]);

        $issue->update($data);

        $description = 'تم تعديل المعوق: «'.$issue->title.'»';
        if ($issue->special_request_id) {
            app(ProjectActivityLogger::class)->logSpecialRequest($issue->special_request_id, $description, 'issue');
        } elseif ($issue->request_id) {
            app(ProjectActivityLogger::class)->logRequest($issue->request_id, $description, 'issue');
        }

        return back()->with('success', 'تم تحديث البيانات بنجاح');
    }

    public function destroy(Issue $issue)
    {
        if ($issue->image && Storage::disk('public')->exists($issue->image)) {
            Storage::disk('public')->delete($issue->image);
        }

        $title = $issue->title;
        $specialId = $issue->special_request_id;
        $requestId = $issue->request_id;

        $issue->delete();

        $description = 'تم حذف المعوق: «'.$title.'»';
        if ($specialId) {
            app(ProjectActivityLogger::class)->logSpecialRequest($specialId, $description, 'issue');
        } elseif ($requestId) {
            app(ProjectActivityLogger::class)->logRequest($requestId, $description, 'issue');
        }

        return back()->with('success', 'تم حذف السجل بنجاح');
    }
}
