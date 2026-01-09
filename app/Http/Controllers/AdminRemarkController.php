<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminRemark;
use App\Models\User;

class AdminRemarkController extends Controller
{
    public function index()
    {
        $remarks = AdminRemark::with('user')->latest()->get();
        return view('dashboard.admin_remarks.index', compact('remarks'));
    }

    public function show(AdminRemark $adminRemark)
    {
        return view('dashboard.admin_remarks.show', ['remark' => $adminRemark]);
    }

    public function edit(AdminRemark $adminRemark)
    {
        $employees = User::where('role', 'partner')->where('is_employee', 1)->get();
        return view('dashboard.admin_remarks.edit', ['remark' => $adminRemark, 'employees' => $employees]);
    }

    public function update(Request $request, AdminRemark $adminRemark)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'details' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('remarks', 'public');
        }

        $adminRemark->update($data);
        return redirect()->route('dashboard.admin_remarks.index')->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(AdminRemark $adminRemark)
    {
        $adminRemark->delete();
        return redirect()->back()->with('success', 'تم الحذف');
    }
    public function create()
    {
        // جلب الموظفين (شركاء + موظف)
        $employees = User::where('role', 'partner')->where('is_employee', 1)->get();
        return view('dashboard.admin_remarks.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'details' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('remarks', 'public');
        }

        AdminRemark::create($data);
        return redirect()->route('dashboard.admin_remarks.index')->with('success', 'تم إضافة الملاحظة بنجاحvalue: ');
    }
}
