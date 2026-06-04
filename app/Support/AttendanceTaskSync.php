<?php

namespace App\Support;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class AttendanceTaskSync
{
    public static function pauseRunningTasks(User $user): int
    {
        $tasks = Task::where('user_id', $user->id)
            ->where('is_timer_running', true)
            ->get();

        $paused = 0;

        foreach ($tasks as $task) {
            self::accumulateRunningTime($task);

            if ($task->status !== 'منتهية') {
                $task->status = 'بالانتظار';
            }

            $task->save();
            $paused++;
        }

        return $paused;
    }

    private static function accumulateRunningTime(Task $task): void
    {
        if (! $task->is_timer_running || ! $task->timer_started_at) {
            return;
        }

        $startedAt = Carbon::parse($task->timer_started_at);
        $seconds = max(0, $startedAt->diffInSeconds(now()));

        $task->tracked_seconds = (int) ($task->tracked_seconds ?? 0) + $seconds;
        $task->timer_started_at = null;
        $task->is_timer_running = false;
    }
}
