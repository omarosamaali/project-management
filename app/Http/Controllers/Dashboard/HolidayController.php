<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Holiday;
use App\Models\User;
use App\Services\WhatsAppOTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HolidayController extends Controller
{
    private function denyUnlessAdmin(): void
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            abort(403, 'غير مصرح لك');
        }
    }

    public function index(Request $request)
    {
        $this->denyUnlessAdmin();

        $holidays = Holiday::withCount('employees')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderByDesc('start_date')
            ->paginate(12);

        return view('dashboard.holidays.index', compact('holidays'));
    }

    public function create()
    {
        $this->denyUnlessAdmin();
        $employees = $this->employeeList();

        return view('dashboard.holidays.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $this->denyUnlessAdmin();
        $data = $this->validated($request);

        $holiday = Holiday::create($data);
        $this->syncEmployees($holiday, $request);
        $this->notifyAffectedEmployees($holiday);

        return redirect()->route('dashboard.holidays.index')
            ->with('success', 'تم حفظ العطلة وإرسال الإشعارات للموظفين المعنيين');
    }

    public function show(Holiday $holiday)
    {
        return redirect()->route('dashboard.holidays.edit', $holiday);
    }

    public function edit(Holiday $holiday)
    {
        $this->denyUnlessAdmin();
        $holiday->load('employees');
        $employees = $this->employeeList();

        return view('dashboard.holidays.edit', compact('holiday', 'employees'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        $this->denyUnlessAdmin();
        $data = $this->validated($request);
        $holiday->update($data);
        $this->syncEmployees($holiday, $request);

        return redirect()->route('dashboard.holidays.index')
            ->with('success', 'تم تحديث العطلة بنجاح');
    }

    public function destroy(Holiday $holiday)
    {
        $this->denyUnlessAdmin();
        $holiday->employees()->detach();
        $holiday->delete();

        return back()->with('success', 'تم حذف العطلة');
    }

    private function employeeList()
    {
        return User::query()
            ->where('role', 'partner')
            ->where('is_employee', true)
            ->notBlocked()
            ->orderBy('name')
            ->get();
    }

    private function validated(Request $request): array
    {
        $rules = [
            'type' => 'required|in:general,private',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'salary_deduction_status' => 'required|in:paid,unpaid',
            'details' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:users,id',
        ];

        if ($request->input('type') === Holiday::TYPE_PRIVATE) {
            $rules['employee_ids'] = 'required|array|min:1';
        }

        $data = $request->validate($rules);
        unset($data['employee_ids']);

        return $data;
    }

    private function syncEmployees(Holiday $holiday, Request $request): void
    {
        if ($holiday->type === Holiday::TYPE_GENERAL) {
            $holiday->employees()->detach();

            return;
        }

        $ids = User::query()
            ->whereIn('id', $request->input('employee_ids', []))
            ->where('role', 'partner')
            ->where('is_employee', true)
            ->notBlocked()
            ->pluck('id');

        $holiday->employees()->sync($ids);
    }

    private function notifyAffectedEmployees(Holiday $holiday): void
    {
        $holiday->load('employees');

        $recipients = $holiday->type === Holiday::TYPE_GENERAL
            ? $this->employeeList()
            : $holiday->employees;

        $range = $holiday->start_date->format('Y-m-d') . ' — ' . $holiday->end_date->format('Y-m-d');
        $salaryNote = $holiday->salary_deduction_status === Holiday::SALARY_PAID
            ? 'العطلة مدفوعة (لا خصم من الراتب).'
            : 'العطلة غير مدفوعة (يُخصم يوم العطلة من الراتب).';

        $message = "تم تسجيل عطلة «{$holiday->name}» ({$holiday->typeLabel()}) من {$range}. {$salaryNote}";
        if ($holiday->details) {
            $message .= ' ' . mb_substr($holiday->details, 0, 120);
        }

        $whatsapp = app(WhatsAppOTPService::class);

        foreach ($recipients as $user) {
            if ($user->isBlocked()) {
                continue;
            }

            AppNotification::notify(
                $user->id,
                'إشعار عطلة',
                $message,
                route('dashboard.work-times.calendar'),
                'fa-umbrella-beach',
                'info'
            );

            if (!$user->phone) {
                continue;
            }

            try {
                $whatsapp->sendHolidayNotification(
                    phone: $user->phone,
                    employeeName: $user->name,
                    holidayName: $holiday->name,
                    typeLabel: $holiday->typeLabel(),
                    dateRange: $range,
                    salaryNote: $salaryNote,
                    details: $holiday->details,
                );

                if ($user->email) {
                    $whatsapp->sendEmailNotification(
                        $user->email,
                        $user->name,
                        'إشعار عطلة — ' . $holiday->name,
                        $message
                    );
                }
            } catch (\Throwable $e) {
                Log::error('[HOLIDAY] فشل إرسال إشعار', [
                    'holiday_id' => $holiday->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
