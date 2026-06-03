<?php

namespace App\Support;

use App\Models\User;
use App\Models\WorkTime;
use Carbon\Carbon;

class WorkAttendanceState
{
    public static function resolve(User $user): array
    {
        $today = Carbon::today()->toDateString();
        $records = WorkTime::where('user_id', $user->id)
            ->where('date', $today)
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        $status = 'off';
        $workedSeconds = 0;
        $currentStart = null;

        foreach ($records as $record) {
            if (in_array($record->type, ['حضور', 'دخول من الاستراحة'], true)) {
                $currentStart = WorkTimeMoment::at($today, $record->start_time);
                $status = 'working';
            } elseif ($record->type === 'خروج للاستراحة') {
                if ($currentStart) {
                    $workedSeconds += $currentStart->diffInSeconds(WorkTimeMoment::at($today, $record->start_time));
                }
                $currentStart = null;
                $status = 'break';
            } elseif ($record->type === 'انصراف') {
                if ($currentStart) {
                    $workedSeconds += $currentStart->diffInSeconds(WorkTimeMoment::at($today, $record->start_time));
                }
                $currentStart = null;
                $status = 'off';
            }
        }

        if ($status === 'working' && $currentStart) {
            $workedSeconds += $currentStart->diffInSeconds(now());
        }

        return [
            'status' => $status,
            'worked_seconds' => max(0, (int) $workedSeconds),
        ];
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'working' => 'يعمل الآن',
            'break' => 'في استراحة',
            default => 'خارج الدوام',
        };
    }

    public static function isEmployeePartner(?User $user): bool
    {
        return $user
            && $user->role === 'partner'
            && (bool) $user->is_employee;
    }
}
