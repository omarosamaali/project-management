<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\IssueComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IssueCommentController extends Controller
{
    /**
     * إضافة تعليق جديد على مشكلة
     */
    // IssueCommentController.php

    public function store(Request $request, Issue $issue)
    {
        $data = $request->validate([
            'comment' => 'required|string',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('issue_comments', 'public');
        }

        $data['user_id'] = auth()->id();
        $data['issue_id'] = $issue->id;

        IssueComment::create($data);

        // تم إزالة كود تحديث الحالة إلى "discussing" بناءً على طلبك
        // الحالة ستبقى كما هي حتى يتم الضغط على "اختيار كحل"

        return back()->with('success', 'تم إضافة التعليق');
    }

    /**
     * تحديد تعليق معين كحل للمشكلة
     */
    public function markAsSolution(Issue $issue, IssueComment $comment)
    {
        // التأكد من أن التعليق ينتمي للمشكلة
        if ($comment->issue_id !== $issue->id) {
            return back()->with('error', 'هذا التعليق لا ينتمي للمشكلة');
        }

        // إلغاء الحل السابق إن وجد
        if ($issue->solutionComment) {
            $issue->solutionComment->update(['is_solution' => false]);
        }

        // تحديث المشكلة
        $issue->update([
            'status' => 'resolved',
            'solution_comment_id' => $comment->id
        ]);

        // تحديث علامة الحل في التعليق
        $comment->update(['is_solution' => true]);

        // تسجيل النشاط
        \App\Models\ProjectActivity::create([
            'special_request_id' => $issue->special_request_id,
            'user_id' => auth()->id(),
            'type' => 'status',
            'description' => 'تم حل المشكلة: ' . $issue->title,
        ]);

        return back()->with('success', 'تم تحديد التعليق كحل للمشكلة');
    }

    /**
     * إلغاء الحل وإعادة المشكلة لحالة "قيد المناقشة"
     */
    public function unmarkSolution(Issue $issue)
    {
        // إلغاء علامة الحل من التعليق
        if ($issue->solutionComment) {
            $issue->solutionComment->update(['is_solution' => false]);
        }

        // تحديث حالة المشكلة
        $issue->update([
            'status' => 'discussing',
            'solution_comment_id' => null
        ]);

        return back()->with('success', 'تم إلغاء الحل وإعادة فتح المشكلة');
    }

    /**
     * حذف تعليق
     */
    public function destroy(IssueComment $comment)
    {
        // التحقق من الصلاحيات (المالك أو الأدمن فقط)
        if ($comment->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return back()->with('error', 'ليس لديك صلاحية لحذف هذا التعليق');
        }

        // منع حذف التعليق إذا كان محدد كحل
        if ($comment->is_solution) {
            return back()->with('error', 'لا يمكن حذف التعليق المحدد كحل. قم بإلغاء الحل أولاً');
        }

        // حذف الصورة إن وجدت
        if ($comment->image && Storage::disk('public')->exists($comment->image)) {
            Storage::disk('public')->delete($comment->image);
        }

        $comment->delete();

        return back()->with('success', 'تم حذف التعليق بنجاح');
    }
}
