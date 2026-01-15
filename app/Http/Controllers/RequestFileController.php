<?php

namespace App\Http\Controllers;

use App\Models\RequestFile;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Storage;

class RequestFileController extends Controller
{
    public function store(HttpRequest $request)
    {
        $request->validate([
            'title' => 'string|max:255',
            'file' => 'file|max:10240',
            'request_id' => 'exists:requests,id'
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

        return back()->with('success', 'تم رفع ملف الطلب بنجاح');
    }

    public function destroy(RequestFile $file)
    {
        Storage::disk('public')->delete($file->file_path);
        $file->delete();
        return back()->with('success', 'تم حذف الملف');
    }
}
