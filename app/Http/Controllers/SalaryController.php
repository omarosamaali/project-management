<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\WorkTime;
use App\Services\WhatsAppOTPService;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $query = Salary::with('user');

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
            'user_id'         => 'required',
            'year'            => 'required',
            'month'           => 'required',
            'overtime_value'  => 'required|numeric',
            'deduction_value' => 'required|numeric',
            'carried_forward' => 'required|numeric',
            'total_due'       => 'required|numeric',
            'attachment'      => 'nullable|image',
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('salaries', 'public');
        }

        $salary->update($data);
        return redirect()->route('salaries.index')->with('success', 'تم تحديث البيانات بنجاح');
    }

    public function create()
    {
        $employees = User::where('role', 'partner')->get();
        return view('dashboard.salaries.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'         => 'required',
            'year'            => 'required',
            'month'           => 'required',
            'overtime_value'  => 'required|numeric',
            'deduction_value' => 'required|numeric',
            'carried_forward' => 'required|numeric',
            'total_due'       => 'required|numeric',
            'attachment'      => 'nullable|image',
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('salaries', 'public');
        }

        $salary = Salary::create($data);

        // ── إرسال إشعار واتساب للموظف ──
        $this->notifyEmployee($salary);

        return redirect()->back()->with('success', 'تم حفظ الراتب وإرسال إشعار للموظف بنجاح');
    }

    public function fetchAttendance($user_id)
    {
        $user       = User::findOrFail($user_id);
        $baseSalary = $user->salary_amount ?? $user->salary_amount_scale ?? 0;

        $hourRate   = ($baseSalary > 0) ? ($baseSalary / 26 / 9) : 0;
        $minuteRate = $hourRate / 60;

        $today = Carbon::today();

        if ($today->day < 25) {
            $startDate = Carbon::create($today->year, $today->month, 25)->subMonths(2)->startOfDay();
            $endDate   = Carbon::create($today->year, $today->month, 24)->subMonth()->endOfDay();
        } else {
            $startDate = Carbon::create($today->year, $today->month, 25)->subMonth()->startOfDay();
            $endDate   = Carbon::create($today->year, $today->month, 24)->endOfDay();
        }

        $records = WorkTime::where('user_id', $user_id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $totalLateMinutes     = 0;
        $totalOvertimeMinutes = 0;
        $totalDeductionAmount = 0;
        $totalOvertimeAmount  = 0;
        $attendanceDays       = 0;

        $groupedByDate = $records->groupBy('date');

        foreach ($groupedByDate as $date => $dayRecords) {
            $checkIn  = $dayRecords->where('type', 'حضور')->first();
            $checkOut = $dayRecords->where('type', 'انصراف')->first();

            if ($checkIn && $checkIn->start_time) {
                $attendanceDays++;
                $checkInTime = Carbon::parse($date . ' ' . $checkIn->start_time);
                $nineAM      = Carbon::parse($date . ' 09:00:00');
                $nineTenAM   = Carbon::parse($date . ' 09:10:00');

                if ($checkInTime->gt($nineTenAM)) {
                    $lateMinutes = $checkInTime->diffInMinutes($nineAM);
                    $totalLateMinutes     += $lateMinutes;
                    $totalDeductionAmount += (90 * $minuteRate);
                } elseif ($checkInTime->gt($nineAM)) {
                    $lateMinutes = $checkInTime->diffInMinutes($nineAM);
                    $totalLateMinutes     += $lateMinutes;
                    $totalDeductionAmount += ($lateMinutes * $minuteRate);
                }
            }

            if ($checkOut && $checkOut->start_time) {
                $checkOutTime = Carbon::parse($date . ' ' . $checkOut->start_time);
                $sixPM        = Carbon::parse($date . ' 18:00:00');

                if ($checkOutTime->gt($sixPM)) {
                    $overtimeMinutes       = $checkOutTime->diffInMinutes($sixPM);
                    $totalOvertimeMinutes += $overtimeMinutes;
                    $totalOvertimeAmount  += ($overtimeMinutes * $minuteRate);
                }
            }
        }

        return response()->json([
            'range'            => $startDate->format('Y/m/d') . ' إلى ' . $endDate->format('Y/m/d'),
            'days_count'       => $attendanceDays,
            'late_minutes'     => (int) abs($totalLateMinutes),
            'overtime_minutes' => (int) abs($totalOvertimeMinutes),
            'deduction_amount' => round(abs($totalDeductionAmount), 2),
            'overtime_amount'  => round(abs($totalOvertimeAmount), 2),
        ]);
    }

    // ── إرسال إشعار الراتب للموظف ──────────────────
    private function notifyEmployee(Salary $salary): void
    {
        try {
            $user = $salary->user ?? User::find($salary->user_id);

            if (!$user || !$user->phone) {
                \Log::info("[SALARY] لا يوجد رقم هاتف للموظف #{$salary->user_id}");
                return;
            }

            $currency = $user->salary_currency ?? $user->salary_currency_scale ?? 'USD';

            \Log::info("[SALARY] إرسال إشعار الراتب", [
                'user_id'   => $user->id,
                'phone'     => $user->phone,
                'total_due' => $salary->total_due,
            ]);

            $whatsapp = app(WhatsAppOTPService::class);
            $sent = $whatsapp->sendSalaryNotification(
                phone: $user->phone,
                employeeName: $user->name,
                totalDue: (float) $salary->total_due,
                currency: $currency,
                month: $salary->month,
                year: $salary->year,
            );

            \Log::info("[SALARY] نتيجة الإرسال: " . ($sent ? 'نجح ✓' : 'فشل ✗'));
        } catch (\Exception $e) {
            \Log::error("[SALARY] فشل إرسال إشعار الراتب", [
                'salary_id' => $salary->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }
}
