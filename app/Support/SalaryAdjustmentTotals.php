<?php

namespace App\Support;

use App\Models\EmployeeAdjustment;
use Carbon\Carbon;

class SalaryAdjustmentTotals
{
    /**
     * مجموع المكافآت والخصومات المسجّلة في شاشة الخصومات والمكافآت لشهر/سنة تقويميين.
     */
    public static function forPeriod(int $userId, int $year, int $month): array
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();

        $records = EmployeeAdjustment::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $bonusTotal = round((float) $records->where('type', 'bonus')->sum('amount'), 2);
        $deductionTotal = round((float) $records->where('type', 'deduction')->sum('amount'), 2);

        return [
            'period_label' => $start->translatedFormat('F Y'),
            'bonus_total' => $bonusTotal,
            'deduction_total' => $deductionTotal,
            'bonus_count' => $records->where('type', 'bonus')->count(),
            'deduction_count' => $records->where('type', 'deduction')->count(),
        ];
    }

    /**
     * دمج مبالغ الخصومات/المكافآت مع حقول الراتب وحساب الإجمالي.
     */
    public static function applyToSalaryPayload(
        int $userId,
        int $year,
        int $month,
        float $baseSalary,
        float $overtimeValue,
        float $carriedForward,
        float $attendanceDeduction
    ): array {
        $adjustments = self::forPeriod($userId, $year, $month);

        $deductionValue = round($attendanceDeduction + $adjustments['deduction_total'], 2);
        $totalDue = round(
            $baseSalary + $overtimeValue + $carriedForward + $adjustments['bonus_total'] - $deductionValue,
            2
        );

        return [
            'overtime_value' => round($overtimeValue, 2),
            'carried_forward' => round($carriedForward, 2),
            'deduction_value' => $deductionValue,
            'total_due' => max(0, $totalDue),
            'adjustment_bonus' => $adjustments['bonus_total'],
            'adjustment_deduction' => $adjustments['deduction_total'],
            'attendance_deduction' => round($attendanceDeduction, 2),
        ];
    }
}
