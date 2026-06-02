<?php

namespace App\Http\Controllers;

use App\Models\WorkTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WorkTimeController extends Controller
{
    private function resolveWorkState(User $user): array
    {
        $today = Carbon::today()->toDateString();
        $records = WorkTime::where('user_id', $user->id)
            ->where('date', $today)
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        $status = 'off';
        $runningSince = null;
        $workedSeconds = 0;
        $currentStart = null;

        foreach ($records as $record) {
            if (in_array($record->type, ['حضور', 'دخول من الاستراحة'])) {
                $currentStart = Carbon::parse($today . ' ' . $record->start_time);
                $status = 'working';
                $runningSince = $currentStart;
            } elseif ($record->type === 'خروج للاستراحة') {
                if ($currentStart) {
                    $workedSeconds += $currentStart->diffInSeconds(Carbon::parse($today . ' ' . $record->start_time));
                }
                $currentStart = null;
                $status = 'break';
                $runningSince = null;
            } elseif ($record->type === 'انصراف') {
                if ($currentStart) {
                    $workedSeconds += $currentStart->diffInSeconds(Carbon::parse($today . ' ' . $record->start_time));
                }
                $currentStart = null;
                $status = 'off';
                $runningSince = null;
            }
        }

        if ($status === 'working' && $currentStart) {
            $workedSeconds += $currentStart->diffInSeconds(now());
        }

        return [
            'status' => $status,
            'worked_seconds' => max(0, (int) $workedSeconds),
            'running_since' => $runningSince?->toIso8601String(),
        ];
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'working' => 'يعمل الآن',
            'break' => 'في استراحة',
            default => 'خارج الدوام',
        };
    }

    public function index(Request $request)
    {
        $query = WorkTime::with('user');
        if ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }
        $workTimes = $query->latest()->paginate(10);
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
        $employees = User::all();
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

    public function quickAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:check_in,break_start,break_end,check_out',
        ]);

        $user = Auth::user();
        if (!$user || $user->role !== 'partner' || !$user->is_employee) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        $state = $this->resolveWorkState($user);
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
            'date' => Carbon::today()->toDateString(),
            'start_time' => now()->format('H:i:s'),
            'timezone' => config('app.timezone', 'UTC'),
            'notes' => 'تسجيل تلقائي من أزرار الدوام',
        ]);

        $newState = $this->resolveWorkState($user);

        return response()->json([
            'ok' => true,
            'status' => $newState['status'],
            'status_label' => $this->statusLabel($newState['status']),
            'worked_seconds' => $newState['worked_seconds'],
        ]);
    }

    public function myStatus()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $state = $this->resolveWorkState($user);
        return response()->json([
            'status' => $state['status'],
            'status_label' => $this->statusLabel($state['status']),
            'worked_seconds' => $state['worked_seconds'],
        ]);
    }
}