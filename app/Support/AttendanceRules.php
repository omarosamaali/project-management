<?php

namespace App\Support;

use App\Models\EmployeeAdjustment;
use App\Models\User;
use App\Models\WorkTime;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceRules
{
    private const DAY_MAP = [
        0 => 'sunday',
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday',
    ];

    public static function dailyWorkHours(User $user): float
    {
        $hours = (float) ($user->daily_work_hours ?? 0);

        return $hours > 0 ? $hours : 9.0;
    }

    public static function hourRate(User $user): float
    {
        $base = (float) ($user->salary_amount ?? $user->salary_amount_scale ?? 0);

        if ($base <= 0) {
            return 0.0;
        }

        return $base / 26 / self::dailyWorkHours($user);
    }

    public static function minuteRate(User $user): float
    {
        return self::hourRate($user) / 60;
    }

    public static function overtimeMinuteRate(User $user): float
    {
        $hourly = (float) ($user->overtime_hourly_rate ?? 0);

        if ($hourly > 0) {
            return $hourly / 60;
        }

        return self::minuteRate($user) * 1.5;
    }

    public static function scheduledEnd(User $user, string $date): Carbon
    {
        $raw = $user->work_end_time ?? '18:00:00';

        try {
            $time = Carbon::parse($raw)->format('H:i:s');
        } catch (\Throwable) {
            $time = '18:00:00';
        }

        return WorkTimeMoment::at($date, $time);
    }

    public static function isWeeklyOff(User $user, Carbon|string $date): bool
    {
        $day = $date instanceof Carbon ? $date->copy() : Carbon::parse($date);
        $vacationDays = $user->vacation_days ?? [];

        if (is_array($vacationDays) && count($vacationDays) > 0) {
            $key = self::DAY_MAP[$day->dayOfWeek] ?? null;

            return $key !== null && in_array($key, $vacationDays, true);
        }

        return $day->isFriday();
    }

    /**
     * @return Collection<int, WorkTime>
     */
    public static function dayRecords(User $user, string $date): Collection
    {
        return WorkTime::where('user_id', $user->id)
            ->where('date', $date)
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array{allowed: bool, blocked: bool, mode: string, message: ?string, adjustment: ?EmployeeAdjustment}
     */
    public static function evaluateCheckIn(User $user, Carbon $at): array
    {
        $date = $at->toDateString();
        $records = self::dayRecords($user, $date);

        if ($records->isEmpty()) {
            return self::evaluateFirstCheckInOfDay($user, $at);
        }

        if (self::isOvertimeCheckInAllowed($user, $records, $date)) {
            return [
                'allowed' => true,
                'blocked' => false,
                'mode' => 'overtime',
                'message' => null,
                'adjustment' => null,
            ];
        }

        return [
            'allowed' => false,
            'blocked' => true,
            'mode' => 'denied',
            'message' => 'لا يمكن تسجيل حضور إلا كجلسة إضافي بعد انصراف بوقت نهاية الدوام أو أكثر.',
            'adjustment' => null,
        ];
    }

    /**
     * @return array{allowed: bool, blocked: bool, mode: string, message: ?string, adjustment: ?EmployeeAdjustment}
     */
    public static function evaluateFirstCheckInOfDay(User $user, Carbon $at): array
    {
        $date = $at->toDateString();
        $scheduledEnd = self::scheduledEnd($user, $date);

        if ($at->gt($scheduledEnd)) {
            $adjustment = self::createAbsenceAdjustment($user, $date, 'تغيّب — أول حضور بعد نهاية الدوام');

            return [
                'allowed' => false,
                'blocked' => true,
                'mode' => 'absence',
                'message' => 'تم تسجيل غياب عن العمل (خصم يوم). لا يمكن تسجيل حضور بعد نهاية الدوام دون جلسة إضافي معتمدة.',
                'adjustment' => $adjustment,
            ];
        }

        return [
            'allowed' => true,
            'blocked' => false,
            'mode' => 'regular',
            'message' => null,
            'adjustment' => null,
        ];
    }

    public static function isOvertimeCheckInAllowed(User $user, Collection $dayRecords, string $date): bool
    {
        $scheduledEnd = self::scheduledEnd($user, $date);

        return $dayRecords
            ->where('type', 'انصراف')
            ->contains(function (WorkTime $record) use ($date, $scheduledEnd) {
                $checkout = WorkTimeMoment::at($date, $record->start_time);

                return $checkout->gte($scheduledEnd);
            });
    }

    public static function lateDeductionForCheckIn(User $user, string $date, Carbon $checkInTime): array
    {
        $scheduledStart = WorkHoursCalculator::scheduledStart($user, $date);
        $allowedLate = (int) ($user->allowed_late_minutes ?? 0);
        $graceEnd = $scheduledStart->copy()->addMinutes($allowedLate);

        if ($checkInTime->lte($graceEnd)) {
            return ['minutes' => 0, 'amount' => 0.0];
        }

        $lateMinutes = (int) $checkInTime->diffInMinutes($scheduledStart);
        $perMinute = (float) ($user->morning_late_deduction ?? 0);
        $rate = $perMinute > 0 ? $perMinute : self::minuteRate($user);
        $billableMinutes = max(0, $lateMinutes - $allowedLate);
        $amount = round($billableMinutes * $rate, 2);

        return ['minutes' => $lateMinutes, 'amount' => $amount];
    }

    public static function evaluateBreakReturn(User $user, string $date, Collection $dayRecords): array
    {
        $allowedBreak = (int) ($user->break_minutes ?? 0);
        if ($allowedBreak <= 0) {
            return ['excess_minutes' => 0, 'amount' => 0.0];
        }

        $breakOut = $dayRecords->where('type', 'خروج للاستراحة')->sortByDesc('start_time')->first();
        $breakIn = $dayRecords->where('type', 'دخول من الاستراحة')->sortByDesc('start_time')->first();

        if (! $breakOut || ! $breakIn) {
            return ['excess_minutes' => 0, 'amount' => 0.0];
        }

        $outAt = WorkTimeMoment::at($date, $breakOut->start_time);
        $inAt = WorkTimeMoment::at($date, $breakIn->start_time);
        $taken = (int) $outAt->diffInMinutes($inAt);

        if ($taken <= $allowedBreak) {
            return ['excess_minutes' => 0, 'amount' => 0.0];
        }

        $excess = $taken - $allowedBreak;
        $perMinute = (float) ($user->break_late_deduction ?? 0);
        $rate = $perMinute > 0 ? $perMinute : self::minuteRate($user);

        return [
            'excess_minutes' => $excess,
            'amount' => round($excess * $rate, 2),
        ];
    }

    public static function evaluateEarlyLeave(User $user, string $date, Carbon $checkoutTime): array
    {
        $scheduledEnd = self::scheduledEnd($user, $date);

        if ($checkoutTime->gte($scheduledEnd)) {
            return ['minutes' => 0, 'amount' => 0.0];
        }

        $earlyMinutes = (int) $scheduledEnd->diffInMinutes($checkoutTime);
        $perMinute = (float) ($user->early_leave_deduction ?? 0);
        $rate = $perMinute > 0 ? $perMinute : self::minuteRate($user);

        return [
            'minutes' => $earlyMinutes,
            'amount' => round($earlyMinutes * $rate, 2),
        ];
    }

    public static function overtimeMinutesForDay(User $user, string $date, Collection $dayRecords): int
    {
        $scheduledEnd = self::scheduledEnd($user, $date);
        $checkouts = $dayRecords->where('type', 'انصراف')->sortBy('start_time')->values();
        $checkIns = $dayRecords->where('type', 'حضور')->sortBy('start_time')->values();

        if ($checkouts->isEmpty() || $checkIns->count() < 2) {
            return 0;
        }

        $qualifyingCheckout = $checkouts->first(function (WorkTime $record) use ($date, $scheduledEnd) {
            return WorkTimeMoment::at($date, $record->start_time)->gte($scheduledEnd);
        });

        if (! $qualifyingCheckout) {
            return 0;
        }

        $checkoutAt = WorkTimeMoment::at($date, $qualifyingCheckout->start_time);
        $overtimeCheckIn = $checkIns->first(function (WorkTime $record) use ($date, $checkoutAt) {
            return WorkTimeMoment::at($date, $record->start_time)->gt($checkoutAt);
        });

        if (! $overtimeCheckIn) {
            return 0;
        }

        $sessionStart = WorkTimeMoment::at($date, $overtimeCheckIn->start_time);
        $finalCheckout = $checkouts
            ->filter(fn (WorkTime $r) => WorkTimeMoment::at($date, $r->start_time)->gt($sessionStart))
            ->sortByDesc('start_time')
            ->first();

        if (! $finalCheckout) {
            return 0;
        }

        $sessionEnd = WorkTimeMoment::at($date, $finalCheckout->start_time);

        return max(0, (int) $sessionStart->diffInMinutes($sessionEnd));
    }

    /**
     * @return array{late_minutes: int, late_amount: float, overtime_minutes: int, overtime_amount: float, break_excess_amount: float, early_leave_amount: float}
     */
    public static function summarizeDay(User $user, string $date, Collection $dayRecords): array
    {
        $firstCheckIn = $dayRecords->first(fn ($r) => $r->type === 'حضور');
        $lateMinutes = 0;
        $lateAmount = 0.0;

        if ($firstCheckIn && $firstCheckIn->start_time) {
            $checkInTime = WorkTimeMoment::at($date, $firstCheckIn->start_time);
            $late = self::lateDeductionForCheckIn($user, $date, $checkInTime);
            $lateMinutes = $late['minutes'];
            $lateAmount = $late['amount'];
        }

        $overtimeMinutes = self::overtimeMinutesForDay($user, $date, $dayRecords);
        $overtimeAmount = round($overtimeMinutes * self::overtimeMinuteRate($user), 2);

        $breakPenalty = self::evaluateBreakReturn($user, $date, $dayRecords);

        $checkout = $dayRecords->where('type', 'انصراف')->sortByDesc('start_time')->first();
        $earlyAmount = 0.0;
        if ($checkout && $checkout->start_time) {
            $early = self::evaluateEarlyLeave($user, $date, WorkTimeMoment::at($date, $checkout->start_time));
            $earlyAmount = $early['amount'];
        }

        return [
            'late_minutes' => $lateMinutes,
            'late_amount' => $lateAmount,
            'overtime_minutes' => $overtimeMinutes,
            'overtime_amount' => $overtimeAmount,
            'break_excess_amount' => $breakPenalty['amount'],
            'early_leave_amount' => $earlyAmount,
        ];
    }

    public static function createAbsenceAdjustment(User $user, string $date, string $notePrefix): ?EmployeeAdjustment
    {
        $noteKey = $notePrefix . ' — ' . $date;
        $exists = EmployeeAdjustment::where('user_id', $user->id)
            ->where('type', 'deduction')
            ->where('date', $date)
            ->where('notes', 'like', $noteKey . '%')
            ->exists();

        if ($exists) {
            return null;
        }

        $amount = HolidayCalendar::dailySalaryRate($user);
        if ($amount <= 0) {
            return null;
        }

        return EmployeeAdjustment::create([
            'user_id' => $user->id,
            'type' => 'deduction',
            'amount' => $amount,
            'date' => $date,
            'notes' => $noteKey,
        ]);
    }

    public static function createDeductionIfNeeded(
        User $user,
        string $date,
        float $amount,
        string $notePrefix,
        bool $createIfZero = false
    ): ?EmployeeAdjustment {
        if ($amount <= 0 && !$createIfZero) {
            return null;
        }

        $noteKey = $notePrefix . ' — ' . $date;
        $exists = EmployeeAdjustment::where('user_id', $user->id)
            ->where('type', 'deduction')
            ->where('date', $date)
            ->where('notes', 'like', $noteKey . '%')
            ->exists();

        if ($exists) {
            return null;
        }

        return EmployeeAdjustment::create([
            'user_id' => $user->id,
            'type' => 'deduction',
            'amount' => round($amount, 2),
            'date' => $date,
            'notes' => $noteKey,
        ]);
    }
}
