<?php

namespace App\Support;

use App\Models\EmployeeAdjustment;
use App\Models\User;
use App\Models\WorkTime;
use Carbon\Carbon;

class EmployeeProfileStats
{
    public static function forUser(User $user): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $monthRecords = WorkTime::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        $scheduledStart = self::normalizeTime($user->work_start_time, '09:00:00');
        $totalLateMinutes = 0;

        foreach ($monthRecords->where('type', 'حضور') as $record) {
            $dateKey = WorkTimeMoment::dateKey($record->date);
            $checkIn = WorkTimeMoment::at($dateKey, $record->start_time);
            $expected = WorkTimeMoment::at($dateKey, $scheduledStart);
            if ($checkIn->gt($expected)) {
                $totalLateMinutes += $checkIn->diffInMinutes($expected);
            }
        }

        $adjustments = EmployeeAdjustment::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get();

        $todayState = WorkAttendanceState::resolve($user);

        return [
            'month_label' => $now->translatedFormat('F Y'),
            'attendance_days' => $monthRecords->where('type', 'حضور')->count(),
            'checkout_days' => $monthRecords->where('type', 'انصراف')->count(),
            'break_sessions' => $monthRecords->where('type', 'خروج للاستراحة')->count(),
            'total_late_minutes' => (int) $totalLateMinutes,
            'total_bonuses' => round((float) $adjustments->where('type', 'bonus')->sum('amount'), 2),
            'total_deductions' => round((float) $adjustments->where('type', 'deduction')->sum('amount'), 2),
            'today_status' => $todayState['status'],
            'today_status_label' => WorkAttendanceState::statusLabel($todayState['status']),
            'today_worked_seconds' => $todayState['worked_seconds'],
        ];
    }

    private static function normalizeTime(?string $time, string $fallback): string
    {
        if (!$time || trim($time) === '') {
            return $fallback;
        }

        return Carbon::parse($time)->format('H:i:s');
    }
}
