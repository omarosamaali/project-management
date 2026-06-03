<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkTime;
use App\Support\WorkAttendanceState;
use App\Support\WorkTimeMoment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkTimeCalendarController extends Controller
{
    public function index(Request $request)
    {
        $isEmployeeView = WorkAttendanceState::isEmployeePartner(Auth::user());
        $employees = collect();
        $selectedUserId = null;

        if ($isEmployeeView) {
            $selectedUserId = Auth::id();
        } else {
            if (!Auth::user() || Auth::user()->role !== 'admin') {
                abort(403, 'غير مصرح لك');
            }
            $employees = User::where('role', 'partner')
                ->where('is_employee', 1)
                ->notBlocked()
                ->orderBy('name')
                ->get(['id', 'name']);
            $selectedUserId = $request->query('user_id');
        }

        return view('dashboard.work-times.calendar', compact(
            'employees',
            'isEmployeeView',
            'selectedUserId'
        ));
    }

    public function events(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([], 403);
        }

        $isEmployeeView = WorkAttendanceState::isEmployeePartner($user);

        if (!$isEmployeeView && $user->role !== 'admin') {
            return response()->json([], 403);
        }

        $request->validate([
            'start' => 'nullable|date',
            'end' => 'nullable|date',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $query = WorkTime::with('user')
            ->whereHas('user', fn ($q) => $q->notBlocked());

        if ($isEmployeeView) {
            $query->where('user_id', $user->id);
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->user_id);
        }

        if ($request->filled('start') && $request->filled('end')) {
            $rangeStart = Carbon::parse($request->start)->startOfDay();
            $rangeEnd = Carbon::parse($request->end)->subSecond()->endOfDay();
            $query->whereDate('date', '>=', $rangeStart->toDateString())
                ->whereDate('date', '<=', $rangeEnd->toDateString());
        }

        $showAllEmployees = !$isEmployeeView && !$request->filled('user_id');

        $events = $query->orderBy('date')->orderBy('start_time')->get()->map(function (WorkTime $record) use ($showAllEmployees) {
            try {
                $start = WorkTimeMoment::at($record->date, $record->start_time);
                $date = WorkTimeMoment::dateKey($record->date);

                $employeeName = $record->user->name ?? 'موظف';
                $title = $showAllEmployees
                    ? "{$record->type} — {$employeeName}"
                    : $record->type;

                $icon = $record->isFromWeb() ? ' 🌐' : '';

                return [
                    'id' => $record->id,
                    'title' => $title . $icon,
                    'start' => $start->toIso8601String(),
                    'allDay' => false,
                    'backgroundColor' => $this->colorForType($record->type),
                    'borderColor' => $this->colorForType($record->type),
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => $record->type,
                        'employee' => $employeeName,
                        'time' => $start->format('g:i A'),
                        'date' => $date,
                        'source' => $record->sourceLabel(),
                        'from_web' => $record->isFromWeb(),
                        'notes' => $record->notes,
                    ],
                ];
            } catch (\Throwable) {
                return null;
            }
        })->filter()->values();

        return response()->json($events);
    }

    private function colorForType(string $type): string
    {
        return match ($type) {
            'حضور' => '#16a34a',
            'انصراف' => '#2563eb',
            'خروج للاستراحة' => '#ca8a04',
            'دخول من الاستراحة' => '#059669',
            default => '#6b7280',
        };
    }
}
