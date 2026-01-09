<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\WorkTime;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon; 
class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $query = Salary::with('user');

        // بحث باسم الموظف إذا أردت
        if ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $salaries = $query->latest()->paginate(10);
        return view('dashboard.salaries.index', compact('salaries'));
    }

    public function show(Salary $salary)
    {
        return view('dashboard.salaries.show', compact('salary'));
    }

    public function edit(Salary $salary)
    {
        $employees = User::where('role', 'partner')->where('is_employee', 1)->get();
        return view('dashboard.salaries.edit', compact('salary', 'employees'));
    }

    public function update(Request $request, Salary $salary)
    {
        $data = $request->validate([
            'user_id' => 'required',
            'year' => 'required',
            'month' => 'required',
            'overtime_value' => 'required|numeric',
            'deduction_value' => 'required|numeric',
            'carried_forward' => 'required|numeric',
            'total_due' => 'required|numeric',
            'attachment' => 'nullable|image',
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('salaries', 'public');
        }

        $salary->update($data);
        return redirect()->route('salaries.index')->with('success', 'تم تحديث البيانات بنجاح');
    }
    
    public function create()
    {
        // جلب الموظفين بالشرط المطلوب
        $employees = User::where('role', 'partner')
            ->where('is_employee', 1)
            ->get();

        return view('dashboard.salaries.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required',
            'year' => 'required',
            'month' => 'required',
            'overtime_value' => 'required|numeric',
            'deduction_value' => 'required|numeric',
            'carried_forward' => 'required|numeric',
            'total_due' => 'required|numeric',
            'attachment' => 'nullable|image',
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('salaries', 'public');
        }

        Salary::create($data);
        return redirect()->back()->with('success', 'تم حفظ الراتب بنجاح');
    }

    public function fetchAttendance($user_id)
    {
        $user = User::findOrFail($user_id);
        $baseSalary = $user->salary_amount ?? $user->salary_amount_scale ?? 0;

        // الحسبة: الراتب / 26 يوم / 9 ساعات
        $hourRate = ($baseSalary > 0) ? ($baseSalary / 26 / 9) : 0;
        $minuteRate = $hourRate / 60;

        // ✅ ضبط النطاق: من 25 الشهر الماضي إلى 24 الشهر الحالي
        $today = Carbon::today();

        // إذا اليوم الحالي قبل يوم 25، نرجع شهرين
        if ($today->day < 25) {
            $startDate = Carbon::create($today->year, $today->month, 25)->subMonths(2)->startOfDay();
            $endDate = Carbon::create($today->year, $today->month, 24)->subMonth()->endOfDay();
        } else {
            // إذا اليوم 25 أو أكتر، نحسب من 25 الشهر الماضي لحد 24 الشهر الحالي
            $startDate = Carbon::create($today->year, $today->month, 25)->subMonth()->startOfDay();
            $endDate = Carbon::create($today->year, $today->month, 24)->endOfDay();
        }

        // جلب جميع السجلات في النطاق
        $records = WorkTime::where('user_id', $user_id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $totalLateMinutes = 0;
        $totalOvertimeMinutes = 0;
        $totalDeductionAmount = 0;
        $totalOvertimeAmount = 0;
        $attendanceDays = 0;

        // تجميع السجلات حسب التاريخ
        $groupedByDate = $records->groupBy('date');

        foreach ($groupedByDate as $date => $dayRecords) {
            $checkIn = $dayRecords->where('type', 'حضور')->first();
            $checkOut = $dayRecords->where('type', 'انصراف')->first();

            // ============= حساب الحضور والتأخير =============
            if ($checkIn && $checkIn->start_time) {
                $attendanceDays++;

                $checkInTime = Carbon::parse($date . ' ' . $checkIn->start_time);
                $nineAM = Carbon::parse($date . ' 09:00:00');
                $nineTenAM = Carbon::parse($date . ' 09:10:00');

                // إذا جاء بعد 9:10 صباحاً
                if ($checkInTime->gt($nineTenAM)) {
                    $lateMinutes = $checkInTime->diffInMinutes($nineAM);
                    $totalLateMinutes += $lateMinutes;
                    // خصم ساعة ونصف (90 دقيقة) كعقوبة
                    $totalDeductionAmount += (90 * $minuteRate);
                }
                // إذا جاء بين 9:00 و 9:10
                elseif ($checkInTime->gt($nineAM)) {
                    $lateMinutes = $checkInTime->diffInMinutes($nineAM);
                    $totalLateMinutes += $lateMinutes;
                    // خصم الدقائق الفعلية فقط
                    $totalDeductionAmount += ($lateMinutes * $minuteRate);
                }
            }

            // ============= حساب الوقت الإضافي =============
            if ($checkOut && $checkOut->start_time) {
                $checkOutTime = Carbon::parse($date . ' ' . $checkOut->start_time);
                $sixPM = Carbon::parse($date . ' 18:00:00');

                // إذا انصرف بعد الساعة 6 مساءً
                if ($checkOutTime->gt($sixPM)) {
                    $overtimeMinutes = $checkOutTime->diffInMinutes($sixPM);
                    $totalOvertimeMinutes += $overtimeMinutes;
                    $totalOvertimeAmount += ($overtimeMinutes * $minuteRate);
                }
            }
        }

        // التأكد من أن الأرقام موجبة
        $totalLateMinutes = abs($totalLateMinutes);
        $totalOvertimeMinutes = abs($totalOvertimeMinutes);
        $totalDeductionAmount = abs($totalDeductionAmount);
        $totalOvertimeAmount = abs($totalOvertimeAmount);

        return response()->json([
            'range' => $startDate->format('Y/m/d') . ' إلى ' . $endDate->format('Y/m/d'),
            'days_count' => $attendanceDays,
            'late_minutes' => (int)$totalLateMinutes,
            'overtime_minutes' => (int)$totalOvertimeMinutes,
            'deduction_amount' => round($totalDeductionAmount, 2),
            'overtime_amount' => round($totalOvertimeAmount, 2)
        ]);
    }
}