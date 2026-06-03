<?php

namespace App\Support;

use App\Models\User;
use App\Models\WorkTime;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WorkHoursCalculator
{
    public static function scheduledStart(User $user, string $date): Carbon
    {
        $raw = $user->work_start_time ?? null;

        if ($raw === null || trim((string) $raw) === '') {
            return WorkTimeMoment::at($date, '09:00:00');
        }

        try {
            $time = Carbon::parse($raw)->format('H:i:s');
        } catch (\Throwable) {
            $time = '09:00:00';
        }

        return WorkTimeMoment::at($date, $time);
    }

    public static function sessionStart(User $user, string $date, string $type, Carbon $checkIn): Carbon
    {
        if ($type === 'حضور') {
            return self::scheduledStart($user, $date);
        }

        return $checkIn;
    }

    /**
     * @param  Collection<int, WorkTime>  $records
     */
    public static function workedSecondsForDay(
        User $user,
        string $date,
        Collection $records,
        ?Carbon $until = null
    ): int {
        $status = 'off';
        $workedSeconds = 0;
        $currentStart = null;
        $hasCheckIn = false;

        foreach ($records as $record) {
            if (in_array($record->type, ['حضور', 'دخول من الاستراحة'], true)) {
                if ($record->type === 'حضور' && $hasCheckIn && $status === 'working') {
                    continue;
                }

                $checkIn = WorkTimeMoment::at($date, $record->start_time);
                $currentStart = self::sessionStart($user, $date, $record->type, $checkIn);
                $status = 'working';

                if ($record->type === 'حضور') {
                    $hasCheckIn = true;
                }
            } elseif ($record->type === 'خروج للاستراحة') {
                if ($currentStart) {
                    $workedSeconds += $currentStart->diffInSeconds(
                        WorkTimeMoment::at($date, $record->start_time)
                    );
                }
                $currentStart = null;
                $status = 'break';
            } elseif ($record->type === 'انصراف') {
                if ($currentStart) {
                    $workedSeconds += $currentStart->diffInSeconds(
                        WorkTimeMoment::at($date, $record->start_time)
                    );
                }
                $currentStart = null;
                $status = 'off';
            }
        }

        if ($status === 'working' && $currentStart) {
            $end = $until ?? now();
            $workedSeconds += $currentStart->diffInSeconds($end);
        }

        return max(0, (int) $workedSeconds);
    }

    public static function resolveState(User $user): array
    {
        $today = Carbon::today()->toDateString();
        $records = WorkTime::where('user_id', $user->id)
            ->where('date', $today)
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        $status = 'off';
        $hasCheckIn = false;

        foreach ($records as $record) {
            if (in_array($record->type, ['حضور', 'دخول من الاستراحة'], true)) {
                if ($record->type === 'حضور' && $hasCheckIn && $status === 'working') {
                    continue;
                }

                $status = 'working';

                if ($record->type === 'حضور') {
                    $hasCheckIn = true;
                }
            } elseif ($record->type === 'خروج للاستراحة') {
                $status = 'break';
            } elseif ($record->type === 'انصراف') {
                $status = 'off';
            }
        }

        $until = $today === Carbon::today()->toDateString() ? now() : null;
        $workedSeconds = self::workedSecondsForDay(
            $user,
            $today,
            $records,
            $until
        );

        return [
            'status' => $status,
            'worked_seconds' => $workedSeconds,
        ];
    }

    public static function isLateCheckIn(User $user, mixed $date, mixed $startTime): bool
    {
        $dateKey = WorkTimeMoment::dateKey($date);
        $checkIn = WorkTimeMoment::at($dateKey, $startTime);
        $scheduledStart = self::scheduledStart($user, $dateKey);

        return $checkIn->gt($scheduledStart);
    }

    public static function scheduledStartLabel(User $user): string
    {
        return CountryNames::formatWorkStart($user->work_start_time);
    }
}
