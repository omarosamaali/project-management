<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpensesController extends Controller
{
    // حفظ مصروف جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'special_request_id' => 'required|exists:special_requests,id',
            'title'              => 'required|string|max:255',
            'price'              => 'required|numeric|min:0',
            'date'               => 'required|date',
            'description'        => 'nullable|string',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('expenses', 'public');
            $validated['image'] = $path;
        }

        $validated['user_id'] = Auth::user()->id;

        Expenses::create($validated);
        \App\Models\ProjectActivity::create([
            'special_request_id' => $request->special_request_id,
            'user_id' => auth()->id(),
            'type' => 'file', // أو status, invoice, etc
            'description' => 'تم اضافة مصروف جديد',
        ]);
        return redirect()->back()->with('success', 'تم إضافة المصروف بنجاح');
    }
    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
            'title'              => 'required|string|max:255',
            'price'              => 'required|numeric|min:0',
            'date'               => 'required|date',
            'description'        => 'nullable|string',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('expenses', 'public');
            $validated['image'] = $path;
        }

        $validated['user_id'] = Auth::user()->id;

        Expenses::create($validated);
        \App\Models\ProjectActivity::create([
            'request_id' => $request->request_id,
            'user_id' => auth()->id(),
            'type' => 'file', // أو status, invoice, etc
            'description' => 'تم اضافة مصروف جديد',
        ]);
        return redirect()->back()->with('success', 'تم إضافة المصروف بنجاح');
    }
    
    // تحديث مصروف موجود
    public function update(Request $request, Expenses $expense)
    {
        // ملاحظة: تأكد أن اسم المتغير في الـ Route هو {expense} ليعمل الـ Binding تلقائياً

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'date'        => 'required|date',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا وجدت
            if ($expense->image) {
                Storage::disk('public')->delete($expense->image);
            }
            $path = $request->file('image')->store('expenses', 'public');
            $validated['image'] = $path;
        }

        $expense->update($validated);

        return redirect()->back()->with('success', 'تم تحديث بيانات المصروف');
    }

    // حذف مصروف
    public function destroy(Expenses $expense)
    {
        // حذف الصورة من التخزين قبل حذف السجل
        if ($expense->image) {
            Storage::disk('public')->delete($expense->image);
        }

        $expense->delete();

        return redirect()->back()->with('success', 'تم حذف المصروف بنجاح');
    }
}
