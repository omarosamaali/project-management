<?php

namespace App\Http\Controllers;

use App\Models\WorkTime;
use App\Models\User;
use Illuminate\Http\Request;

class WorkTimeController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkTime::with('user');
        if ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $workTimes = $query->latest()->paginate(10);

        // إحصائيات سريعة (مثل نموذج الطلبات)
        $allCount = WorkTime::count();
        $attendanceCount = WorkTime::where('type', 'حضور')->count();
        $leaveCount = WorkTime::where('type', 'انصراف')->count();

        return view('dashboard.work-times.index', compact('workTimes', 'allCount', 'attendanceCount', 'leaveCount'));
    }

    public function create()
    {
        $employees = User::where('is_employee', 1)->get();
        return view('dashboard.work-times.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'type' => 'required',
            'date' => 'required|date',
            'start_time' => 'required',
        ]);

        WorkTime::create($request->all());
        return redirect()->route('dashboard.work-times.index')->with('success', 'تم تسجيل الوقت بنجاح');
    }

    public function edit(WorkTime $workTime)
    {
        $employees = User::all(); // جلب الموظفين لعمل القائمة المنسدلة
        return view('dashboard.work-times.edit', compact('workTime', 'employees'));
    }

    public function update(Request $request, WorkTime $workTime)
    {
        $workTime->update($request->all());
        return redirect()->route('dashboard.work-times.index')->with('success', 'تم تحديث البيانات بنجاح');
    }

    public function destroy(WorkTime $workTime)
    {
        $workTime->delete();
        return redirect()->route('dashboard.work-times.index')->with('success', 'تم حذف السجل بنجاح');
    }
}