<?php

namespace App\Support;

use App\Models\PartnerSystem;
use App\Models\Payment;
use App\Models\Requests;
use App\Models\SpecialRequest;
use App\Models\SpecialRequestPartner;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkTime;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CockpitMetrics
{
    /**
     * @return array{0: \Illuminate\Database\Eloquent\Builder, 1: \Illuminate\Database\Eloquent\Builder}
     */
    public static function projectQueriesFor(User $user): array
    {
        if ($user->role === 'admin') {
            return [Requests::query(), SpecialRequest::query()];
        }

        if ($user->role === 'partner') {
            $systemIds = PartnerSystem::where('partner_id', $user->id)->pluck('system_id');
            $assignedIds = SpecialRequestPartner::where('partner_id', $user->id)
                ->whereNotNull('special_request_id')
                ->pluck('special_request_id');

            return [
                Requests::whereIn('system_id', $systemIds),
                SpecialRequest::whereIn('id', $assignedIds),
            ];
        }

        $reqIds = DB::table('request_clients')->where('user_id', $user->id)->pluck('request_id');
        $specialIds = DB::table('special_request_clients')->where('user_id', $user->id)->pluck('special_request_id');

        return [
            Requests::whereIn('id', $reqIds),
            SpecialRequest::whereIn('id', $specialIds),
        ];
    }

    public static function projectStatsFor(User $user): array
    {
        [$baseReq, $baseSpecial] = self::projectQueriesFor($user);

        $newStatusesRequests = ['new', 'جديد'];
        $underProcessStatusesRequests = ['in_progress', 'تحت الاجراء', 'waiting_client'];
        $pendingStatusesRequests = ['pending', 'معلقة', 'suspended'];
        $closedStatusesRequests = ['closed', 'completed', 'منتهية'];

        $newStatusesSpecial = ['pending', 'جديد'];
        $underProcessStatusesSpecial = ['in_progress', 'تحت الاجراء', 'active', 'in_review'];
        $pendingStatusesSpecial = ['معلقة', 'بانتظار الدفع', 'بانتظار عروض الاسعار'];
        $closedStatusesSpecial = ['completed', 'canceled', 'منتهية'];

        return [
            'all' => (clone $baseReq)->count() + (clone $baseSpecial)->count(),
            'new' => (clone $baseReq)->whereIn('status', $newStatusesRequests)->count()
                + (clone $baseSpecial)->whereIn('status', $newStatusesSpecial)->count(),
            'in_progress' => (clone $baseReq)->whereIn('status', $underProcessStatusesRequests)->count()
                + (clone $baseSpecial)->whereIn('status', $underProcessStatusesSpecial)->count(),
            'pending' => (clone $baseReq)->whereIn('status', $pendingStatusesRequests)->count()
                + (clone $baseSpecial)->whereIn('status', $pendingStatusesSpecial)->count(),
            'closed' => (clone $baseReq)->whereIn('status', $closedStatusesRequests)->count()
                + (clone $baseSpecial)->whereIn('status', $closedStatusesSpecial)->count(),
        ];
    }

    public static function courseStatsFor(User $user): array
    {
        $now = Carbon::now();
        $payments = Payment::where('user_id', $user->id)
            ->whereNotNull('course_id')
            ->with('course')
            ->get();

        $withCourse = $payments->filter(fn ($p) => $p->course !== null);

        return [
            'all' => $withCourse->count(),
            'active' => $withCourse->filter(fn ($p) => $now->between(
                Carbon::parse($p->course->start_date)->startOfDay(),
                Carbon::parse($p->course->end_date)->endOfDay()
            ))->count(),
            'upcoming' => $withCourse->filter(fn ($p) => $now->lt(
                Carbon::parse($p->course->start_date)->startOfDay()
            ))->count(),
            'ended' => $withCourse->filter(fn ($p) => $now->gt(
                Carbon::parse($p->course->end_date)->endOfDay()
            ))->count(),
        ];
    }

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
            $tasks = $query->where('user_id', $user->id)->get();
        } else {
            $tasks = $query->where('user_id', $user->id)->get();
        }

        return self::buildTaskStats($tasks, false);
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
        if (! WorkAttendanceState::isEmployeePartner($user)) {
            return null;
        }

        try {
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
                'status_today' => WorkAttendanceState::statusLabel($state['status'], $user),
                'late_minutes' => $payroll['late_minutes'],
                'overtime_amount' => $payroll['overtime_amount'],
                'attendance_deduction' => $payroll['deduction_amount'],
                'bonus_total' => $adjustments['bonus_total'],
                'adjustment_deduction' => $adjustments['deduction_total'],
                'attendance_days' => $payroll['days_count'],
            ];
        } catch (\Throwable $e) {
            Log::warning('[COCKPIT] attendanceStatsFor failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'period_label' => now()->translatedFormat('F Y'),
                'worked_hours' => 0,
                'worked_hours_today' => 0,
                'status_today' => WorkAttendanceState::statusLabel('off', $user),
                'late_minutes' => 0,
                'overtime_amount' => 0,
                'attendance_deduction' => 0,
                'bonus_total' => 0,
                'adjustment_deduction' => 0,
                'attendance_days' => 0,
            ];
        }
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
            $until = $date === Carbon::today()->toDateString() ? now() : null;
            $totalSeconds += WorkHoursCalculator::workedSecondsForDay($user, $date, $dayRecords, $until);
        }

        return max(0, (int) $totalSeconds);
    }
}
