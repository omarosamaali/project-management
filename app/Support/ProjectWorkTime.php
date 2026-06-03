<?php

namespace App\Support;

use App\Models\SpecialRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProjectWorkTime
{
    public static function workHoursBetweenDates(?string $startDate, ?string $endDate): int
    {
        if (!$startDate || !$endDate) {
            return 0;
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->startOfDay();

        if ($end->lt($start)) {
            return 0;
        }

        $calendarDays  = (int) $start->diffInDays($end);
        $fullWeeks     = intdiv($calendarDays, 7);
        $remainingDays = $calendarDays % 7;
        $workDays      = ($fullWeeks * SpecialRequest::WORK_DAYS_PER_WEEK)
            + min($remainingDays, SpecialRequest::WORK_DAYS_PER_WEEK);

        return $workDays * SpecialRequest::WORK_HOURS_PER_DAY;
    }

    public static function expectedSeconds(Model $project): int
    {
        $tasks = $project->relationLoaded('tasks') ? $project->tasks : $project->tasks()->get();
        $hoursFromTasks = (int) $tasks->sum(
            fn ($task) => self::workHoursBetweenDates($task->start_date, $task->end_date)
        );

        if ($hoursFromTasks > 0) {
            return $hoursFromTasks * 3600;
        }

        if ($project instanceof SpecialRequest) {
            $deadlineHours = self::deadlineExpectedHours($project);
            if ($deadlineHours > 0) {
                return $deadlineHours * 3600;
            }
        }

        $stages = $project->relationLoaded('stages') ? $project->stages : $project->stages()->get();
        $stageHours = (float) $stages->sum('hours_count');

        return (int) round($stageHours * 3600);
    }

    /**
     * مجموع الوقت المخزّن فقط (tracked_seconds) — عمل فعلي سجّله الفريق عبر العداد.
     */
    public static function spentSeconds(Model $project): int
    {
        $tasks = $project->relationLoaded('tasks') ? $project->tasks : $project->tasks()->get();

        return (int) $tasks->sum(fn ($task) => (int) $task->stored_tracked_seconds);
    }

    /**
     * @return array<int, array{user_id: int|null, user_name: string, seconds: int, label: string}>
     */
    public static function spentByWorkers(Model $project): array
    {
        $tasks = $project->relationLoaded('tasks')
            ? $project->tasks->loadMissing('user')
            : $project->tasks()->with('user')->get();

        return $tasks
            ->groupBy('user_id')
            ->map(function ($group, $userId) {
                $seconds = (int) $group->sum(fn ($task) => (int) $task->stored_tracked_seconds);
                $user    = $group->first()->user;
                $name    = $user ? SystemManager::nameFor($user) : 'غير معيّن';

                return [
                    'user_id'   => $userId !== '' ? (int) $userId : null,
                    'user_name' => $name,
                    'seconds'   => $seconds,
                    'label'     => DurationFormatter::format($seconds),
                ];
            })
            ->filter(fn ($row) => $row['seconds'] > 0)
            ->sortByDesc('seconds')
            ->values()
            ->all();
    }

    public static function remainingSeconds(Model $project): int
    {
        return max(self::expectedSeconds($project) - self::spentSeconds($project), 0);
    }

    public static function timeProgressPercent(int $spentSeconds, int $expectedSeconds): float
    {
        if ($expectedSeconds <= 0) {
            return 0.0;
        }

        return min(round(($spentSeconds / $expectedSeconds) * 100, 1), 100.0);
    }

    public static function completionProgressPercent(Model $project): float
    {
        $tasks = $project->relationLoaded('tasks') ? $project->tasks : $project->tasks()->get();

        if ($tasks->count() > 0) {
            $done = $tasks->where('status', 'منتهية')->count();

            return round(($done / $tasks->count()) * 100, 1);
        }

        $stages = $project->relationLoaded('stages') ? $project->stages : $project->stages()->get();

        if ($stages->count() > 0) {
            $done = $stages->where('status', 'completed')->count();

            return round(($done / $stages->count()) * 100, 1);
        }

        return 0.0;
    }

    public static function progressPercent(Model $project): float
    {
        $spent    = self::spentSeconds($project);
        $expected = self::expectedSeconds($project);
        $timePct  = self::timeProgressPercent($spent, $expected);
        $donePct  = self::completionProgressPercent($project);

        return min(max($timePct, $donePct), 100.0);
    }

    /** تقدم الوقت المعتمد على العمل المخزّن فقط. */
    public static function storedTimeProgressPercent(Model $project): float
    {
        return self::timeProgressPercent(self::spentSeconds($project), self::expectedSeconds($project));
    }

    private static function deadlineExpectedHours(SpecialRequest $project): int
    {
        if (!$project->deadline || !$project->created_at) {
            return 0;
        }

        $start = Carbon::parse($project->created_at)->startOfDay();
        $end   = Carbon::parse($project->deadline)->startOfDay();
        $days  = (int) $start->diffInDays($end);

        $fullWeeks   = intdiv($days, 7);
        $remainDays  = $days % 7;
        $workDays    = ($fullWeeks * SpecialRequest::WORK_DAYS_PER_WEEK)
            + min($remainDays, SpecialRequest::WORK_DAYS_PER_WEEK);

        return $workDays * SpecialRequest::WORK_HOURS_PER_DAY;
    }
}
