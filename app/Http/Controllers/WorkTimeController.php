<?php

namespace App\Http\Controllers;

use App\Models\WorkTime;
use App\Models\User;
use App\Support\CountryNames;
use App\Support\CountryTimezone;
use App\Support\WorkAttendanceState;
use App\Support\WorkHoursCalculator;
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

        $workTimes = $query->latest()->paginate(10);

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

        if ($data['type'] === 'حضور' && WorkHoursCalculator::isLateCheckIn($employee, $data['date'], $data['start_time'])) {
            $countFrom = WorkHoursCalculator::scheduledStartLabel($employee);
            $autoNote = 'احتساب من ' . $countFrom;
            $data['notes'] = trim(($data['notes'] ?? '') . ' ' . $autoNote);
        }

        WorkTime::create($data);
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
        $workTime->update($request->all());
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

        if ($action === 'check_in' && $state['status'] !== 'off') {
            return response()->json(['message' => 'تم تسجيل الحضور مسبقاً اليوم'], 422);
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

        WorkTime::create([
            'user_id' => $user->id,
            'country' => strtoupper($user->country ?? 'AE'),
            'type' => $typeMap[$action],
            'source' => WorkTime::SOURCE_WEB,
            'date' => Carbon::today()->toDateString(),
            'start_time' => now()->format('H:i:s'),
            'timezone' => config('app.timezone', 'UTC'),
            'notes' => 'تسجيل من أزرار الدوام في الموقع',
        ]);

        $newState = WorkAttendanceState::resolve($user);

        return response()->json([
            'ok' => true,
            'status' => $newState['status'],
            'status_label' => WorkAttendanceState::statusLabel($newState['status']),
            'worked_seconds' => $newState['worked_seconds'],
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
            'status_label' => WorkAttendanceState::statusLabel($state['status']),
            'worked_seconds' => $state['worked_seconds'],
        ]);
    }
}
