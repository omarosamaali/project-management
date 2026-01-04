<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KbCategory;
use Illuminate\Http\Request;

class KbCategoryController extends Controller
{
    public function index()
    {
        $categories = KbCategory::latest()->get();
        return view('dashboard.knowledge_base.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        KbCategory::create($request->all());

        return back()->with('success', 'تم إضافة التصنيف بنجاح');
    }

    public function edit(KbCategory $kbCategory)
    {
        return view('dashboard.knowledge_base.categories.edit', compact('kbCategory'));
    }

    public function update(Request $request, KbCategory $kbCategory)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $kbCategory->update($request->all());

        return redirect()->route('dashboard.kb_categories.index')->with('success', 'تم تحديث التصنيف');
    }

    public function destroy(KbCategory $kbCategory)
    {
        $kbCategory->delete();
        return back()->with('success', 'تم حذف التصنيف بنجاح');
    }
}
