<?php

namespace App\Http\Controllers;

use App\Models\ProjectFile;
use App\Services\ProjectActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectFileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240',
        ]);

        $path = $request->file('file')->store('project_files', 'public');

        ProjectFile::create([
            'special_request_id' => $request->special_request_id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_type' => $request->file('file')->getClientOriginalExtension(),
        ]);

        app(ProjectActivityLogger::class)->logSpecialRequest(
            (int) $request->special_request_id,
            'تم رفع ملف جديد: «'.$request->title.'»',
            'file',
        );

        return back()->with('success', 'تم رفع الملف بنجاح');
    }

    public function update(Request $request, ProjectFile $file)
    {
        $file->update($request->only('title', 'description'));

        app(ProjectActivityLogger::class)->logSpecialRequest(
            $file->special_request_id,
            'تم تعديل ملف: «'.$file->title.'»',
            'file',
        );

        return back()->with('success', 'تم تحديث البيانات');
    }

    public function destroy(ProjectFile $file)
    {
        $title = $file->title;
        $projectId = $file->special_request_id;

        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        app(ProjectActivityLogger::class)->logSpecialRequest(
            $projectId,
            'تم حذف ملف: «'.$title.'»',
            'file',
        );

        return back()->with('success', 'تم حذف الملف');
    }
}
