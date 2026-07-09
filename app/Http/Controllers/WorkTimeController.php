<?php

namespace App\Http\Controllers;

use App\Models\WorkTime;
use App\Models\User;
use App\Support\AttendanceRules;
use App\Support\AttendanceTaskSync;
use App\Support\CountryNames;
use App\Support\CountryTimezone;
use App\Support\WorkAttendanceState;
use App\Support\WorkHoursCalculator;
use App\Support\WorkTimeMoment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WorkTimeController extends Controller
{
    private function denyUnlessAdmin(): void
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'غير مصرح لك');
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isEmployeeView = WorkAttendanceState::isEmployeePartner($user);

        $query = WorkTime::with('user')
            ->whereHas('user', fn ($u) => $u->notBlocked());

        if ($isEmployeeView) {
            $query->where('user_id', $user->id);
        } elseif ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $workTimes = $query
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->paginate(10);

        if ($isEmployeeView) {
            $allCount = WorkTime::where('user_id', $user->id)->count();
            $attendanceCount = WorkTime::where('user_id', $user->id)->where('type', 'حضور')->count();
            $leaveCount = WorkTime::where('user_id', $user->id)->where('type', 'انصراف')->count();
        } else {
            $allCount = WorkTime::count();
            $attendanceCount = WorkTime::where('type', 'حضور')->count();
            $leaveCount = WorkTime::where('type', 'انصراف')->count();
        }

        return view('dashboard.work-times.index', compact(
            'workTimes',
            'allCount',
            'attendanceCount',
            'leaveCount',
            'isEmployeeView'
        ));
    }

    public function create()
    {
        $this->denyUnlessAdmin();
        $employees = User::where('is_employee', 1)
            ->notBlocked()
            ->get(['id', 'name', 'country', 'work_start_time'])
            ->map(fn (User $emp) => (object) [
                'id' => $emp->id,
                'name' => CountryNames::ensureUtf8($emp->name) ?? '',
                'country_code' => strtoupper((string) ($emp->country ?? '')),
                'country_name' => CountryNames::forCode($emp->country) ?? '',
                'work_start' => CountryNames::formatWorkStart($emp->work_start_time),
            ]);

        return view('dashboard.work-times.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $this->denyUnlessAdmin();
        $data = $request->validate([
            'user_id' => 'required',
            'country' => 'required|string|max:5',
            'type' => 'required',
            'date' => 'required|date',
            'start_time' => 'required',
            'timezone' => 'nullable|string|max:64',
            'notes' => 'nullable|string',
        ]);

        $employee = User::notBlocked()->where('is_employee', 1)->findOrFail($data['user_id']);
        if (! $employee->country) {
            return back()->withInput()->withErrors([
                'country' => 'الموظف المختار ليس لديه دولة مسجّلة في ملفه.',
            ]);
        }
        $data['country'] = strtoupper($employee->country);

        $data['source'] = WorkTime::SOURCE_MANUAL;
        $data['timezone'] = $data['timezone']
            ?? CountryTimezone::timezoneForCountry($data['country']);

        if ($data['type'] === 'حضور') {
            $at = WorkTimeMoment::at($data['date'], $data['start_time']);
            $records = AttendanceRules::dayRecords($employee, WorkTimeMoment::dateKey($data['date']));

            if ($records->isEmpty()) {
                $evaluation = AttendanceRules::evaluateFirstCheckInOfDay($employee, $at);
                if ($evaluation['blocked']) {
                    return back()->withInput()->withErrors([
                        'type' => $evaluation['message'] ?? 'لا يمكن تسجيل هذا الحضور.',
                    ]);
                }
            } elseif (! AttendanceRules::isOvertimeCheckInAllowed($employee, $records, WorkTimeMoment::dateKey($data['date']))) {
                $existingCheckIn = $records->where('type', 'حضور')->count();
                if ($existingCheckIn >= 1) {
                    return back()->withInput()->withErrors([
                        'type' => 'حضور إضافي يتطلب انصرافاً بعد نهاية الدوام أولاً.',
                    ]);
                }
            }

            if (WorkHoursCalculator::isLateCheckIn($employee, $data['date'], $data['start_time'])) {
                $countFrom = WorkHoursCalculator::scheduledStartLabel($employee);
                $autoNote = 'احتساب من ' . $countFrom;
                $data['notes'] = trim(($data['notes'] ?? '') . ' ' . $autoNote);
            }
        }

        WorkTime::create($data);

        $this->applyPostRecordRules($employee, $data['type'], WorkTimeMoment::dateKey($data['date']), WorkTimeMoment::at($data['date'], $data['start_time']));

        return redirect()->route('dashboard.work-times.index')->with('success', 'تم تسجيل الوقت بنجاح');
    }

    public function edit(WorkTime $workTime)
    {
        $this->denyUnlessAdmin();
        $employees = User::all();
        return view('dashboard.work-times.edit', compact('workTime', 'employees'));
    }

    public function update(Request $request, WorkTime $workTime)
    {
        $this->denyUnlessAdmin();
        $data = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'country'    => 'required|string|max:5',
            'type'       => 'required|string',
            'date'       => 'required|date',
            'start_time' => 'required',
            'end_time'   => 'nullable',
            'notes'      => 'nullable|string',
            'timezone'   => 'nullable|string|max:64',
        ]);
        $workTime->update($data);
        return redirect()->route('dashboard.work-times.index')->with('success', 'تم تحديث البيانات بنجاح');
    }

    public function destroy(WorkTime $workTime)
    {
        $this->denyUnlessAdmin();
        $workTime->delete();
        return redirect()->route('dashboard.work-times.index')->with('success', 'تم حذف السجل بنجاح');
    }

    public function countryTime(Request $request)
    {
        $this->denyUnlessAdmin();

        $countryCode = $request->query('country');
        $user = null;

        if ($request->filled('user_id')) {
            $user = User::notBlocked()->find($request->query('user_id'));
            if ($user && $user->country) {
                $countryCode = $user->country;
            }
        }

        if (!$countryCode && $request->boolean('use_ip')) {
            $fromIp = CountryTimezone::detectFromIp($request->ip());
            if ($fromIp) {
                return response()->json(array_merge($fromIp, [
                    'work_start' => $user
                        ? (CountryTimezone::localNow($fromIp['country_code'], $user)['work_start'] ?? '09:00')
                        : '09:00',
                ]));
            }
        }

        if (!$countryCode) {
            return response()->json(['message' => 'حدد الدولة أو الموظف'], 422);
        }

        return response()->json(CountryTimezone::localNow($countryCode, $user));
    }

    public function quickAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:check_in,break_start,break_end,check_out',
        ]);

        $user = Auth::user();
        if (!WorkAttendanceState::isEmployeePartner($user)) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        $state = WorkAttendanceState::resolve($user);
        $action = $request->action;
        $now = now();
        $today = $now->toDateString();

        if ($action === 'check_in') {
            if ($state['status'] !== 'off') {
                return response()->json(['message' => 'أنت مسجّل حضوراً بالفعل أو في استراحة'], 422);
            }

            $evaluation = AttendanceRules::evaluateCheckIn($user, $now);
            if ($evaluation['blocked'] || ! $evaluation['allowed']) {
                return response()->json([
                    'message' => $evaluation['message'] ?? 'لا يمكن تسجيل الحضور',
                ], 422);
            }
        }

        if ($action === 'break_start' && $state['status'] !== 'working') {
            return response()->json(['message' => 'لا يمكن بدء الاستراحة الآن'], 422);
        }
        if ($action === 'break_end' && $state['status'] !== 'break') {
            return response()->json(['message' => 'لا توجد استراحة مفتوحة حالياً'], 422);
        }
        if ($action === 'check_out' && $state['status'] === 'off') {
            return response()->json(['message' => 'لم يتم تسجيل حضور بعد'], 422);
        }

        $typeMap = [
            'check_in' => 'حضور',
            'break_start' => 'خروج للاستراحة',
            'break_end' => 'دخول من الاستراحة',
            'check_out' => 'انصراف',
        ];

        $type = $typeMap[$action];

        WorkTime::create([
            'user_id' => $user->id,
            'country' => strtoupper($user->country ?? 'AE'),
            'type' => $type,
            'source' => WorkTime::SOURCE_WEB,
            'date' => $today,
            'start_time' => $now->format('H:i:s'),
            'timezone' => config('app.timezone', 'UTC'),
            'notes' => 'تسجيل من أزرار الدوام في الموقع',
        ]);

        if (in_array($action, ['break_start', 'check_out'], true)) {
            AttendanceTaskSync::pauseRunningTasks($user);
        }

        $this->applyPostRecordRules($user, $type, $today, $now);

        $newState = WorkAttendanceState::resolve($user);

        $lateMessage = null;
        if ($action === 'check_in') {
            $late = AttendanceRules::lateDeductionForCheckIn($user, $today, $now);
            if ($late['minutes'] > 0) {
                $allowedLate = (int) ($user->allowed_late_minutes ?? 0);
                $billable = max(0, $late['minutes'] - $allowedLate);
                $lateMessage = 'تنبيه: أنت متأخر ' . $late['minutes'] . ' دقيقة عن بداية الدوام';
                if ($allowedLate > 0) {
                    $lateMessage .= ' (المسموح: ' . $allowedLate . ' دقيقة)';
                }
                if ($late['amount'] > 0) {
                    $lateMessage .= ' — تم احتساب خصم: ' . number_format($late['amount'], 2);
                } elseif ($billable > 0) {
                    $lateMessage .= ' — لم يُحتسب خصم (يرجى تحديث بيانات الراتب)';
                }
            }
        }

        return response()->json([
            'ok' => true,
            'status' => $newState['status'],
            'status_label' => WorkAttendanceState::statusLabel($newState['status'], $user),
            'worked_seconds' => $newState['worked_seconds'],
            'late_message' => $lateMessage,
        ]);
    }

    public function myStatus()
    {
        $user = Auth::user();
        if (!WorkAttendanceState::isEmployeePartner($user)) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $state = WorkAttendanceState::resolve($user);
        return response()->json([
            'status' => $state['status'],
            'status_label' => WorkAttendanceState::statusLabel($state['status'], $user),
            'worked_seconds' => $state['worked_seconds'],
        ]);
    }

    private function applyPostRecordRules(User $user, string $type, string $date, Carbon $at): void
    {
        $records = AttendanceRules::dayRecords($user, $date);

        if ($type === 'حضور' && $records->where('type', 'حضور')->count() === 1) {
            $late = AttendanceRules::lateDeductionForCheckIn($user, $date, $at);
            if ($late['minutes'] > 0) {
                AttendanceRules::createDeductionIfNeeded(
                    $user,
                    $date,
                    $late['amount'],
                    'خصم تأخير صباحي',
                    createIfZero: true
                );
            }
        }

        if ($type === 'دخول من الاستراحة') {
            $break = AttendanceRules::evaluateBreakReturn($user, $date, $records);
            AttendanceRules::createDeductionIfNeeded(
                $user,
                $date,
                $break['amount'],
                'خصم تجاوز وقت الاستراحة'
            );
        }

        if ($type === 'انصراف') {
            $early = AttendanceRules::evaluateEarlyLeave($user, $date, $at);
            AttendanceRules::createDeductionIfNeeded(
                $user,
                $date,
                $early['amount'],
                'خصم خروج مبكر'
            );
        }
    }
}
