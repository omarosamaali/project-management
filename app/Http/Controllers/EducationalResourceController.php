<?php

namespace App\Http\Controllers;

use App\Models\EducationalResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducationalResourceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = EducationalResource::query();

        // Filter based on user role
        // إذا كان المستخدم ليس admin، اعرض فقط الفيديوهات المتاحة له
        if ($user && $user->role !== 'admin') {
            $query->whereJsonContains('users', $user->role);
        }

        // Optional: Additional filters (only for admin)
        if ($user && $user->role === 'admin') {
            if ($request->has('user_type') && $request->user_type != 'all') {
                $query->whereJsonContains('users', $request->user_type);
            }

            if ($request->has('language') && $request->language != 'all') {
                $query->where('language', $request->language);
            }

            if ($request->has('status') && $request->status != 'all') {
                $query->where('status', $request->status);
            }
        } else {
            // Non-admin users only see active videos
            $query->where('status', 1);
        }

        $resources = $query->latest()->get();

        return view('dashboard.educational_resources.index', compact('resources'));
    }


    public function create()
    {
        return view('dashboard.educational_resources.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'language' => 'required|in:ar,en',
            'users' => 'required|array', // غيّرها لـ array
            'users.*' => 'in:partner,independent_partner,client', // تحقق من القيم
            'youtube_url' => 'required|url',
            'status' => 'required|boolean',
        ]);

        EducationalResource::create($request->all());
        return redirect()->route('dashboard.educational_resources.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function edit(EducationalResource $educational_resource)
    {
        return view('dashboard.educational_resources.edit', compact('educational_resource'));
    }

    public function update(Request $request, EducationalResource $educational_resource)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'language' => 'required|in:ar,en',
            'users' => 'required|array',
            'users.*' => 'in:partner,independent_partner,client',
            'youtube_url' => 'required|url',
            'status' => 'required|boolean',
        ]);

        $educational_resource->update($request->all());
        return redirect()->route('dashboard.educational_resources.index')->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(EducationalResource $resource)
    {
        $resource->delete();
        return back()->with('success', 'تم الحذف بنجاح');
    }
}
