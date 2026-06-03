<?php

namespace App\Support;

use App\Models\Task;
use App\Models\User;
use App\Models\WorkTime;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CockpitMetrics
{
    /**
     * مهام المستخدم (مسؤول عنها) ضمن المشاريع المتاحة.
     */
    public static function taskStatsFor(User $user, array $requestIds, array $specialIds): array
    {
        $query = Task::query();

        if ($user->role !== 'admin') {
            $query->where(function ($q) use ($requestIds, $specialIds) {
                if (!empty($specialIds)) {
                    $q->whereIn('special_request_id', $specialIds);
                }
                if (!empty($requestIds)) {
                    $q->orWhereIn('request_id', $requestIds);
                }
                if (empty($specialIds) && empty($requestIds)) {
                    $q->whereRaw('0 = 1');
                }
            });
        }

        if ($user->role === 'admin') {
            $tasks = $query->get();
        } else {
            $tasks = $query->where('user_id', $user->id)->get();
        }

        return self::buildTaskStats($tasks, $user->role === 'admin');
    }

    private static function buildTaskStats(Collection $tasks, bool $isAdminScope): array
    {
        $now = Carbon::now();
        $completed = $tasks->where('status', 'منتهية');
        $completedCount = $completed->count();
        $total = $tasks->count();
        $remaining = $total - $completedCount;

        $lateStatus = $tasks->where('status', 'متأخرة')->count();
        $overdue = $tasks->filter(function (Task $t) use ($now) {
            if ($t->status === 'منتهية' || !$t->end_date) {
                return false;
            }

            return Carbon::parse($t->end_date)->endOfDay()->lt($now);
        })->count();

        $completedWithDates = $completed->filter(fn (Task $t) => $t->start_date && $t->end_date);
        $avgDays = null;
        if ($completedWithDates->isNotEmpty()) {
            $avgDays = round($completedWithDates->avg(function (Task $t) {
                return max(1, Carbon::parse($t->start_date)->diffInDays(Carbon::parse($t->end_date)) + 1);
            }), 1);
        }

        $onTimeCompleted = $completed->filter(function (Task $t) {
            if (!$t->end_date) {
                return true;
            }
            $finished = $t->updated_at ?? Carbon::parse($t->end_date);

            return $finished->lte(Carbon::parse($t->end_date)->endOfDay());
        })->count();

        $onTimeRate = $completedCount > 0
            ? round(($onTimeCompleted / $completedCount) * 100)
            : null;

        return [
            'scope_label' => $isAdminScope ? 'جميع المهام' : 'مهامي',
            'total' => $total,
            'completed' => $completedCount,
            'remaining' => max(0, $remaining),
            'in_progress' => $tasks->where('status', 'قيد الإنجاز')->count(),
            'waiting' => $tasks->where('status', 'بالانتظار')->count(),
            'late' => max($lateStatus, $overdue),
            'overdue' => $overdue,
            'avg_completion_days' => $avgDays,
            'on_time_rate' => $onTimeRate,
            'total_tracked_hours' => round($tasks->sum(fn (Task $t) => $t->elapsed_tracked_seconds) / 3600, 1),
        ];
    }

    /**
     * إحصائيات حضور الشهر الحالي (للموظفين).
     */
    public static function attendanceStatsFor(User $user): ?array
    {
        if (!WorkAttendanceState::isEmployeePartner($user)) {
            return null;
        }

        $year = (int) now()->year;
        $month = (int) now()->month;

        $workedSeconds = self::workedSecondsInCalendarMonth($user, $year, $month);
        $payroll = SalaryAttendanceSummary::forPeriod($user->id, $year, $month, $user);
        $adjustments = SalaryAdjustmentTotals::forPeriod($user->id, $year, $month);

        $state = WorkAttendanceState::resolve($user);

        return [
            'period_label' => $payroll['period_label'] ?? now()->translatedFormat('F Y'),
            'worked_hours' => round($workedSeconds / 3600, 1),
            'worked_hours_today' => round(($state['worked_seconds'] ?? 0) / 3600, 1),
            'status_today' => WorkAttendanceState::statusLabel($state['status']),
            'late_minutes' => $payroll['late_minutes'],
            'overtime_amount' => $payroll['overtime_amount'],
            'attendance_deduction' => $payroll['deduction_amount'],
            'bonus_total' => $adjustments['bonus_total'],
            'adjustment_deduction' => $adjustments['deduction_total'],
            'attendance_days' => $payroll['days_count'],
        ];
    }

    public static function workedSecondsInCalendarMonth(User $user, int $year, int $month): int
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();

        $records = WorkTime::where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        $totalSeconds = 0;

        foreach ($records->groupBy(fn ($r) => WorkTimeMoment::dateKey($r->date)) as $date => $dayRecords) {
            $totalSeconds += self::workedSecondsForDay($date, $dayRecords);
        }

        return max(0, (int) $totalSeconds);
    }

    /**
     * @param \Illuminate\Support\Collection<int, WorkTime> $dayRecords
     */
    private static function workedSecondsForDay(string $date, $dayRecords): int
    {
        $seconds = 0;
        $currentStart = null;

        foreach ($dayRecords as $record) {
            if (in_array($record->type, ['حضور', 'دخول من الاستراحة'], true)) {
                $currentStart = WorkTimeMoment::at($date, $record->start_time);
            } elseif ($record->type === 'خروج للاستراحة') {
                if ($currentStart) {
                    $seconds += $currentStart->diffInSeconds(WorkTimeMoment::at($date, $record->start_time));
                }
                $currentStart = null;
            } elseif ($record->type === 'انصراف') {
                if ($currentStart) {
                    $seconds += $currentStart->diffInSeconds(WorkTimeMoment::at($date, $record->start_time));
                }
                $currentStart = null;
            }
        }

        if ($currentStart && WorkTimeMoment::dateKey($date) === Carbon::today()->toDateString()) {
            $seconds += $currentStart->diffInSeconds(now());
        }

        return max(0, (int) $seconds);
    }
}
