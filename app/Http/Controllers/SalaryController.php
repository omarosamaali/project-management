<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Services\WhatsAppOTPService;
use App\Support\SalaryAdjustmentTotals;
use App\Support\SalaryAttendanceSummary;
use App\Support\WorkAttendanceState;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SalaryController extends Controller
{
    private function isEmployeeView(): bool
    {
        return WorkAttendanceState::isEmployeePartner(Auth::user());
    }

    private function denyUnlessAdmin(): void
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            abort(403, 'غير مصرح لك');
        }
    }

    private function authorizeSalaryAccess(Salary $salary): void
    {
        if ($this->isEmployeeView() && (int) $salary->user_id !== (int) Auth::id()) {
            abort(403, 'غير مصرح لك');
        }
    }

    public function index(Request $request)
    {
        $isEmployeeView = $this->isEmployeeView();
        $query = Salary::with('user')
            ->whereHas('user', fn ($u) => $u->notBlocked());

        if ($isEmployeeView) {
            $query->where('user_id', Auth::id());
        } elseif ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $salaries = $query->latest()->paginate(10);
        return view('dashboard.salaries.index', compact('salaries', 'isEmployeeView'));
    }

    public function show(Salary $salary)
    {
        $this->authorizeSalaryAccess($salary);
        $isEmployeeView = $this->isEmployeeView();
        return view('dashboard.salaries.show', compact('salary', 'isEmployeeView'));
    }

    public function edit(Salary $salary)
    {
        $this->denyUnlessAdmin();
        $employees = User::where('role', 'partner')->where('is_employee', 1)->notBlocked()->get();
        return view('dashboard.salaries.edit', compact('salary', 'employees'));
    }

    public function update(Request $request, Salary $salary)
    {
        $this->denyUnlessAdmin();
        $data = $request->validate([
            'user_id'              => 'required',
            'year'                 => 'required|integer',
            'month'                => 'required|integer|min:1|max:12',
            'overtime_value'       => 'nullable|numeric',
            'deduction_value'      => 'nullable|numeric',
            'carried_forward'      => 'nullable|numeric',
            'total_due'            => 'nullable|numeric',
            'attendance_deduction' => 'nullable|numeric',
            'attachment'           => 'nullable|image',
        ]);

        $data = $this->mergeSalaryWithAdjustments($data, $request);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('salaries', 'public');
        }

        $salary->update($data);
        return redirect()->route('dashboard.salaries.index')->with('success', 'تم تحديث البيانات بنجاح');
    }

    public function destroy(Salary $salary)
    {
        $this->denyUnlessAdmin();

        if ($salary->attachment) {
            Storage::disk('public')->delete($salary->attachment);
        }

        $salary->delete();

        return redirect()
            ->route('dashboard.salaries.index')
            ->with('success', 'تم حذف سجل الراتب بنجاح');
    }

    public function create()
    {
        $this->denyUnlessAdmin();
        $employees = User::where('role', 'partner')->notBlocked()->get();
        return view('dashboard.salaries.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $this->denyUnlessAdmin();
        $data = $request->validate([
            'user_id'              => 'required',
            'year'                 => 'required|integer',
            'month'                => 'required|integer|min:1|max:12',
            'overtime_value'       => 'nullable|numeric',
            'deduction_value'      => 'nullable|numeric',
            'carried_forward'      => 'nullable|numeric',
            'total_due'            => 'nullable|numeric',
            'attendance_deduction' => 'nullable|numeric',
            'attachment'           => 'nullable|image',
        ]);

        $data = $this->mergeSalaryWithAdjustments($data, $request);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('salaries', 'public');
        }

        $salary = Salary::create($data);

        // ── إرسال إشعار واتساب للموظف ──
        $this->notifyEmployee($salary);

        return redirect()->back()->with('success', 'تم حفظ الراتب وإرسال إشعار للموظف بنجاح');
    }

    public function fetchAttendance(Request $request, $user_id)
    {
        $this->denyUnlessAdmin();

        $request->validate([
            'year'  => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $user = User::notBlocked()->findOrFail($user_id);

        return response()->json(
            SalaryAttendanceSummary::forPeriod(
                (int) $user_id,
                (int) $request->year,
                (int) $request->month,
                $user
            )
        );
    }

    public function fetchAdjustments(Request $request, $user_id)
    {
        $this->denyUnlessAdmin();

        $request->validate([
            'year'  => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        User::notBlocked()->findOrFail($user_id);

        $totals = SalaryAdjustmentTotals::forPeriod(
            (int) $user_id,
            (int) $request->year,
            (int) $request->month
        );

        return response()->json($totals);
    }

    private function baseSalaryFor(User $user): float
    {
        return (float) ($user->salary_amount ?? $user->salary_amount_scale ?? 0);
    }

    private function mergeSalaryWithAdjustments(array $data, Request $request): array
    {
        $user = User::notBlocked()->findOrFail($data['user_id']);

        $merged = SalaryAdjustmentTotals::applyToSalaryPayload(
            (int) $data['user_id'],
            (int) $data['year'],
            (int) $data['month'],
            $this->baseSalaryFor($user),
            (float) $data['overtime_value'],
            (float) $data['carried_forward'],
            (float) $request->input('attendance_deduction', 0)
        );

        return array_merge($data, [
            'overtime_value'  => $merged['overtime_value'],
            'carried_forward' => $merged['carried_forward'],
            'deduction_value' => $merged['deduction_value'],
            'total_due'       => $merged['total_due'],
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
