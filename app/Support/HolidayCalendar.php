<?php

namespace App\Support;

use App\Models\Holiday;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class HolidayCalendar
{
    /**
     * هل اليوم عطلة للموظف (عامة نشطة أو خاصة تشمله)؟
     */
    public static function isHolidayForUser(User $user, Carbon|string $date): bool
    {
        $day = $date instanceof Carbon ? $date->copy()->startOfDay() : Carbon::parse($date)->startOfDay();

        return self::activeHolidaysOn($day)->contains(function (Holiday $holiday) use ($user, $day) {
            return self::holidayAppliesToUserOnDate($holiday, $user, $day);
        });
    }

    /**
     * يوم لا يُحسب عملاً ولا يُسجَّل غياب عليه (جمعة أو عطلة).
     */
    public static function isNonWorkingDay(User $user, Carbon|string $date): bool
    {
        $day = $date instanceof Carbon ? $date->copy() : Carbon::parse($date);

        if (AttendanceRules::isWeeklyOff($user, $day)) {
            return true;
        }

        return self::isHolidayForUser($user, $day);
    }

    /**
     * أيام عطلة غير مدفوعة للموظف ضمن فترة (لخصم الراتب).
     */
    public static function unpaidHolidayDaysInPeriod(User $user, Carbon $start, Carbon $end): int
    {
        $count = 0;
        $period = CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay());

        foreach ($period as $day) {
            if (AttendanceRules::isWeeklyOff($user, $day)) {
                continue;
            }

            $holiday = self::holidayForUserOnDate($user, $day);
            if ($holiday && $holiday->salary_deduction_status === Holiday::SALARY_UNPAID) {
                $count++;
            }
        }

        return $count;
    }

    public static function dailySalaryRate(User $user): float
    {
        $base = (float) ($user->salary_amount ?? $user->salary_amount_scale ?? 0);

        return $base > 0 ? round($base / 26, 2) : 0.0;
    }

    public static function holidayForUserOnDate(User $user, Carbon $day): ?Holiday
    {
        return self::activeHolidaysOn($day)->first(function (Holiday $holiday) use ($user, $day) {
            return self::holidayAppliesToUserOnDate($holiday, $user, $day);
        });
    }

    private static function holidayAppliesToUserOnDate(Holiday $holiday, User $user, Carbon $day): bool
    {
        if ($holiday->type === Holiday::TYPE_GENERAL) {
            return true;
        }

        if (!$holiday->relationLoaded('employees')) {
            $holiday->load('employees');
        }

        return $holiday->employees->contains('id', $user->id);
    }

    /**
     * @return Collection<int, Holiday>
     */
    private static function activeHolidaysOn(Carbon $day): Collection
    {
        $dateStr = $day->toDateString();

        return Holiday::query()
            ->where('status', Holiday::STATUS_ACTIVE)
            ->whereDate('start_date', '<=', $dateStr)
            ->whereDate('end_date', '>=', $dateStr)
            ->with(['employees'])
            ->get();
    }
}
