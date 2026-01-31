<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use App\Models\KbCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        $categories = KbCategory::with(['creator', 'updater'])
            ->withCount('knowledges')
            ->where('status', 1)
            ->get();
        $query = KnowledgeBase::with(['category', 'user'])->latest();
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }
        if (Auth::user()->role == 'admin') {
            $knowledges = $query->paginate(10)->withQueryString();
        } elseif (Auth::user()->role == 'independent_partner') {
            $knowledges = $query->where('added_by', Auth::user()->id)->paginate(10)->withQueryString();
        } elseif (Auth::user()->role == 'partner') {
            $knowledges = $query->paginate(10)->withQueryString();
        }
        // $knowledges = $query->paginate(10)->withQueryString();

        return view('dashboard.knowledge_base.index', compact('knowledges', 'categories'));
    }

    public function create()
    {
        $categories = KbCategory::where('status', 1)->get();
        return view('dashboard.knowledge_base.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:kb_categories,id',
            'title' => 'required|string|max:255',
            'details' => 'required|string',
            'attachments' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg,zip|max:5120', // حد أقصى 5 ميجا
        ]);

        if ($request->hasFile('attachments')) {
            // حفظ الملف في مجلد public/uploads/kb
            $data['attachments'] = $request->file('attachments')->store('uploads/kb', 'public');
        }

        $data['added_by'] = auth()->id();

        KnowledgeBase::create($data);

        return redirect()->route('dashboard.kb.index')->with('success', 'تم حفظ المعلومة في بنك المعلومات');
    }

    public function edit(KnowledgeBase $kb)
    {
        $categories = KbCategory::where('status', 1)->get();
        return view('dashboard.knowledge_base.edit', compact('kb', 'categories'));
    }

    public function update(Request $request, KnowledgeBase $kb)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:kb_categories,id',
            'title' => 'required|string|max:255',
            'details' => 'required|string',
            'attachments' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('attachments')) {
            // حذف القديم إذا وُجد
            if ($kb->attachments) {
                Storage::disk('public')->delete($kb->attachments);
            }
            $data['attachments'] = $request->file('attachments')->store('uploads/kb', 'public');
        }

        $kb->update($data);

        return redirect()->route('dashboard.kb.index')->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(KnowledgeBase $kb)
    {
        // حذف الملف المرفق من السيرفر قبل حذف السجل
        if ($kb->attachments) {
            Storage::disk('public')->delete($kb->attachments);
        }

        $kb->delete();
        return back()->with('success', 'تم حذف المعلومة بنجاح');
    }
}
