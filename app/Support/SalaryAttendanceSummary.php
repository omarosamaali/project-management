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

        $records = WorkTime::where('user_id', $userId)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        $totalLateMinutes = 0;
        $totalOvertimeMinutes = 0;
        $totalDeductionAmount = 0.0;
        $totalOvertimeAmount = 0.0;
        $attendanceDays = 0;

        foreach ($records->groupBy(fn ($r) => WorkTimeMoment::dateKey($r->date)) as $date => $dayRecords) {
            $hasCheckIn = $dayRecords->contains(fn ($r) => in_array($r->type, ['حضور', 'دخول من الاستراحة'], true));

            if ($hasCheckIn) {
                $attendanceDays++;
            }

            $summary = AttendanceRules::summarizeDay($user, $date, $dayRecords);
            $totalLateMinutes += $summary['late_minutes'];
            $totalDeductionAmount += $summary['late_amount']
                + $summary['break_excess_amount']
                + $summary['early_leave_amount'];
            $totalOvertimeMinutes += $summary['overtime_minutes'];
            $totalOvertimeAmount += $summary['overtime_amount'];
        }

        $unpaidHolidayDays = HolidayCalendar::unpaidHolidayDaysInPeriod($user, $startDate, $endDate);
        $dayRate = HolidayCalendar::dailySalaryRate($user);
        $holidayDeduction = round($unpaidHolidayDays * $dayRate, 2);
        $deductionAmount = round($totalDeductionAmount + $holidayDeduction, 2);
        $overtimeAmount = round($totalOvertimeAmount, 2);

        return [
            'range' => $startDate->format('Y/m/d') . ' إلى ' . $endDate->format('Y/m/d'),
            'period_label' => $startDate->format('Y/m/d') . ' — ' . $endDate->format('Y/m/d'),
            'year' => $year,
            'month' => $month,
            'days_count' => $attendanceDays,
            'unpaid_holiday_days' => $unpaidHolidayDays,
            'holiday_deduction' => $holidayDeduction,
            'late_minutes' => (int) $totalLateMinutes,
            'overtime_minutes' => (int) $totalOvertimeMinutes,
            'deduction_amount' => $deductionAmount,
            'overtime_amount' => $overtimeAmount,
            'net_amount' => round($overtimeAmount - $deductionAmount, 2),
        ];
    }
}
