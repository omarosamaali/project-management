<?php

namespace App\Support;

use App\Models\User;

class WorkAttendanceState
{
    public static function resolve(User $user): array
    {
        return WorkHoursCalculator::resolveState($user);
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
