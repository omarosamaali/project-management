<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use App\Services\ProjectActivityLogger;
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

        app(ProjectActivityLogger::class)->logSpecialRequest(
            (int) $request->special_request_id,
            'تم إضافة مصروف جديد: «'.$validated['title'].'» ('.$validated['price'].' جنيه)',
            'expense',
        );

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

        app(ProjectActivityLogger::class)->logRequest(
            (int) $request->request_id,
            'تم إضافة مصروف جديد: «'.$validated['title'].'» ('.$validated['price'].' جنيه)',
            'expense',
        );

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

        $description = 'تم تعديل مصروف: «'.$expense->title.'»';
        if ($expense->special_request_id) {
            app(ProjectActivityLogger::class)->logSpecialRequest($expense->special_request_id, $description, 'expense');
        } elseif ($expense->request_id) {
            app(ProjectActivityLogger::class)->logRequest($expense->request_id, $description, 'expense');
        }

        return redirect()->back()->with('success', 'تم تحديث بيانات المصروف');
    }

    public function destroy(Expenses $expense)
    {
        if ($expense->image) {
            Storage::disk('public')->delete($expense->image);
        }

        $title = $expense->title;
        $specialId = $expense->special_request_id;
        $requestId = $expense->request_id;

        $expense->delete();

        $description = 'تم حذف مصروف: «'.$title.'»';
        if ($specialId) {
            app(ProjectActivityLogger::class)->logSpecialRequest($specialId, $description, 'expense');
        } elseif ($requestId) {
            app(ProjectActivityLogger::class)->logRequest($requestId, $description, 'expense');
        }

        return redirect()->back()->with('success', 'تم حذف المصروف بنجاح');
    }
}
