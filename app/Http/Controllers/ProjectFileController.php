<?php

namespace App\Http\Controllers;

use App\Models\ProjectFile;
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
        \App\Models\ProjectActivity::create([
            'special_request_id' => $request->special_request_id,
            'user_id' => auth()->id(),
            'type' => 'file', // أو status, invoice, etc
            'description' => 'تم رفع ملف جديد للمشروع',
        ]);
        return back()->with('success', 'تم رفع الملف بنجاح');
    }

    public function update(Request $request, ProjectFile $file)
    {
        $file->update($request->only('title', 'description'));
        return back()->with('success', 'تم تحديث البيانات');
    }

    public function destroy(ProjectFile $file)
    {
        Storage::disk('public')->delete($file->file_path);
        $file->delete();
        return back()->with('success', 'تم حذف الملف');
    }
}
