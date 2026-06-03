<?php

namespace App\Http\Controllers;

use App\Models\RequestsExpense;
use App\Services\ProjectActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpensesRequestController extends Controller
{
    public function store(Request $request)
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

        RequestsExpense::create($validated);

        app(ProjectActivityLogger::class)->logRequest(
            (int) $request->request_id,
            'تم إضافة مصروف جديد: «'.$validated['title'].'» ('.$validated['price'].' جنيه)',
            'expense',
        );

        return redirect()->back()->with('success', 'تم إضافة المصروف بنجاح');
    }

    public function update(Request $request, RequestsExpense $expense)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'date'        => 'required|date',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($expense->image) {
                Storage::disk('public')->delete($expense->image);
            }
            $path = $request->file('image')->store('expenses', 'public');
            $validated['image'] = $path;
        }

        $expense->update($validated);

        app(ProjectActivityLogger::class)->logRequest(
            $expense->request_id,
            'تم تعديل مصروف: «'.$expense->title.'»',
            'expense',
        );

        return redirect()->back()->with('success', 'تم تحديث بيانات المصروف');
    }

    public function destroy(RequestsExpense $expense)
    {
        if ($expense->image) {
            Storage::disk('public')->delete($expense->image);
        }

        $title = $expense->title;
        $requestId = $expense->request_id;

        $expense->delete();

        app(ProjectActivityLogger::class)->logRequest(
            $requestId,
            'تم حذف مصروف: «'.$title.'»',
            'expense',
        );

        return redirect()->back()->with('success', 'تم حذف المصروف بنجاح');
    }
}
