<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAdjustment;
use App\Models\User;
use Illuminate\Http\Request;

class AdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $adjustments = EmployeeAdjustment::with('user')
            ->when($request->search, function ($q) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"));
            })
            ->latest()
            ->paginate(10);

        return view('dashboard.adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $employees = User::where('role', 'partner')->get(); // أو حسب الكود لديك
        return view('dashboard.adjustments.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:bonus,deduction',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        EmployeeAdjustment::create($data);
        return redirect()->route('dashboard.adjustments.index')->with('success', 'تم حفظ السجل بنجاح');
    }

    public function edit(EmployeeAdjustment $adjustment)
    {
        $employees = User::where('role', 'partner')->get();
        return view('dashboard.adjustments.edit', compact('adjustment', 'employees'));
    }
    public function update(Request $request, $id)
    {
        // 1. التحقق من البيانات
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type'    => 'required|in:bonus,deduction',
            'amount'  => 'required|numeric|min:0',
            'date'    => 'required|date',
            'notes'   => 'nullable|string',
        ]);

        // 2. جلب السجل المطلوب تعديله
        $adjustment = EmployeeAdjustment::findOrFail($id);

        // 3. التحديث باستخدام الكائن (باستخدام -> وليس ::)
        $adjustment->update($data);

        // 4. الرد
        return redirect()->route('dashboard.adjustments.index')
            ->with('success', 'تم تعديل السجل بنجاح');
    }

    public function destroy(EmployeeAdjustment $adjustment)
    {
        $adjustment->delete();
        return back()->with('success', 'تم الحذف بنجاح');
    }
}