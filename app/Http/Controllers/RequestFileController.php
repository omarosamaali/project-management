<?php

namespace App\Http\Controllers;

use App\Models\RequestFile;
use App\Services\ProjectActivityLogger;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Storage;

class RequestFileController extends Controller
{
    public function store(HttpRequest $request)
    {
        $request->validate([
            'title' => 'string|max:255',
            'file' => 'file|max:10240',
            'request_id' => 'exists:requests,id',
        ]);

        $path = $request->file('file')->store('request_files', 'public');

        RequestFile::create([
            'request_id' => $request->request_id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_type' => $request->file('file')->getClientOriginalExtension(),
        ]);

        app(ProjectActivityLogger::class)->logRequest(
            (int) $request->request_id,
            'تم رفع ملف جديد: «'.$request->title.'»',
            'file',
        );

        return back()->with('success', 'تم رفع ملف الطلب بنجاح');
    }

    public function destroy(RequestFile $file)
    {
        $title = $file->title;
        $requestId = $file->request_id;

        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        app(ProjectActivityLogger::class)->logRequest(
            $requestId,
            'تم حذف ملف: «'.$title.'»',
            'file',
        );

        return back()->with('success', 'تم حذف الملف');
    }
}
