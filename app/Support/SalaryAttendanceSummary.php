<?php

namespace App\Support;

use App\Models\User;
use App\Models\WorkTime;
use Carbon\Carbon;

class SalaryAttendanceSummary
{
    /**
     * فترة الحضور لراتب شهر معيّن: من 25 الشهر السابق إلى 24 الشهر المحدد.
     */
    public static function payrollPeriodBounds(int $year, int $month): array
    {
        $endDate = Carbon::createFromDate($year, $month, 24)->endOfDay();
        $startDate = Carbon::createFromDate($year, $month, 25)->subMonth()->startOfDay();

        return [$startDate, $endDate];
    }

    public static function forPeriod(int $userId, int $year, int $month, ?User $user = null): array
    {
        $user = $user ?? User::notBlocked()->findOrFail($userId);
        [$startDate, $endDate] = self::payrollPeriodBounds($year, $month);

        $baseSalary = (float) ($user->salary_amount ?? $user->salary_amount_scale ?? 0);
        $hourRate = $baseSalary > 0 ? ($baseSalary / 26 / 9) : 0;
        $minuteRate = $hourRate / 60;

        $workStart = self::normalizeTime($user->work_start_time, '09:00:00');
        $workEnd = self::normalizeTime($user->work_end_time, '18:00:00');
        $graceMinutes = 10;

        $records = WorkTime::where('user_id', $userId)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        $totalLateMinutes = 0;
        $totalOvertimeMinutes = 0;
        $totalDeductionAmount = 0;
        $totalOvertimeAmount = 0;
        $attendanceDays = 0;

        foreach ($records->groupBy('date') as $date => $dayRecords) {
            $checkIn = $dayRecords->first(fn ($r) => $r->type === 'حضور')
                ?? $dayRecords->first(fn ($r) => $r->type === 'دخول من الاستراحة');

            $checkOut = $dayRecords->where('type', 'انصراف')->sortByDesc('start_time')->first();

            if ($checkIn && $checkIn->start_time) {
                $attendanceDays++;
                $checkInTime = Carbon::parse($date . ' ' . $checkIn->start_time);
                $scheduledStart = Carbon::parse($date . ' ' . $workStart);
                $graceEnd = $scheduledStart->copy()->addMinutes($graceMinutes);

                if ($checkInTime->gt($graceEnd)) {
                    $lateMinutes = $checkInTime->diffInMinutes($scheduledStart);
                    $totalLateMinutes += $lateMinutes;
                    $totalDeductionAmount += (90 * $minuteRate);
                } elseif ($checkInTime->gt($scheduledStart)) {
                    $lateMinutes = $checkInTime->diffInMinutes($scheduledStart);
                    $totalLateMinutes += $lateMinutes;
                    $totalDeductionAmount += ($lateMinutes * $minuteRate);
                }
            }

            if ($checkOut && $checkOut->start_time) {
                $checkOutTime = Carbon::parse($date . ' ' . $checkOut->start_time);
                $scheduledEnd = Carbon::parse($date . ' ' . $workEnd);

                if ($checkOutTime->gt($scheduledEnd)) {
                    $overtimeMinutes = $checkOutTime->diffInMinutes($scheduledEnd);
                    $totalOvertimeMinutes += $overtimeMinutes;
                    $totalOvertimeAmount += ($overtimeMinutes * $minuteRate);
                }
            }
        }

        $unpaidHolidayDays = HolidayCalendar::unpaidHolidayDaysInPeriod($user, $startDate, $endDate);
        $dayRate = HolidayCalendar::dailySalaryRate($user);
        $holidayDeduction = round($unpaidHolidayDays * $dayRate, 2);
        $deductionAmount = round(abs($totalDeductionAmount) + $holidayDeduction, 2);

        $overtimeAmount = round(abs($totalOvertimeAmount), 2);

        return [
            'range' => $startDate->format('Y/m/d') . ' إلى ' . $endDate->format('Y/m/d'),
            'period_label' => $startDate->format('Y/m/d') . ' — ' . $endDate->format('Y/m/d'),
            'year' => $year,
            'month' => $month,
            'days_count' => $attendanceDays,
            'unpaid_holiday_days' => $unpaidHolidayDays,
            'holiday_deduction' => $holidayDeduction,
            'late_minutes' => (int) abs($totalLateMinutes),
            'overtime_minutes' => (int) abs($totalOvertimeMinutes),
            'deduction_amount' => $deductionAmount,
            'overtime_amount' => $overtimeAmount,
            'net_amount' => round($overtimeAmount - $deductionAmount, 2),
        ];
    }

    private static function normalizeTime(?string $time, string $fallback): string
    {
        if (!$time || trim($time) === '') {
            return $fallback;
        }

        $parsed = Carbon::parse($time);

        return $parsed->format('H:i:s');
    }
}
