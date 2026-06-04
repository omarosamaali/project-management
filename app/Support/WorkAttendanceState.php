<?php

namespace App\Support;

use App\Models\User;

class WorkAttendanceState
{
    public static function resolve(User $user): array
    {
        return WorkHoursCalculator::resolveState($user);
    }

    public static function statusLabel(string $status, ?User $user = null): string
    {
        if ($user && $status === 'working' && self::isOvertimeSession($user)) {
            return 'جلسة إضافي';
        }

        return match ($status) {
            'working' => 'يعمل الآن',
            'break' => 'في استراحة',
            default => 'خارج الدوام',
        };
    }

    public static function isOvertimeSession(User $user): bool
    {
        $today = now()->toDateString();
        $records = AttendanceRules::dayRecords($user, $today);
        $checkIns = $records->where('type', 'حضور')->count();

        return $checkIns >= 2
            && AttendanceRules::isOvertimeCheckInAllowed($user, $records, $today)
            && WorkHoursCalculator::resolveState($user)['status'] === 'working';
    }

    public static function isEmployeePartner(?User $user): bool
    {
        return $user
            && $user->role === 'partner'
            && (bool) $user->is_employee;
    }
}
