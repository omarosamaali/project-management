<?php

namespace App\Support;

use App\Models\Payment;
use App\Models\TrainerCashoutRequest;
use App\Models\User;

class TrainerProfitWallet
{
    private const SUCCESSFUL_PAYMENT_STATUSES = ['completed', 'success', 'paid', 'active'];

    private const PENDING_CASHOUT_STATUSES = [
        TrainerCashoutRequest::STATUS_PENDING_ADMIN,
        TrainerCashoutRequest::STATUS_PROCESSING,
        TrainerCashoutRequest::STATUS_PENDING_TRAINER_CONFIRM,
    ];

    public static function totalEarned(User $trainer): float
    {
        $total = Payment::query()
            ->join('courses', 'courses.id', '=', 'payments.course_id')
            ->where('courses.trainer_id', $trainer->id)
            ->whereIn('payments.status', self::SUCCESSFUL_PAYMENT_STATUSES)
            ->sum('payments.trainer_profit_amount');

        return round((float) $total, 2);
    }

    public static function withdrawn(User $trainer): float
    {
        $total = TrainerCashoutRequest::query()
            ->where('user_id', $trainer->id)
            ->where('status', TrainerCashoutRequest::STATUS_PAID)
            ->sum('amount');

        return round((float) $total, 2);
    }

    public static function pending(User $trainer): float
    {
        $total = TrainerCashoutRequest::query()
            ->where('user_id', $trainer->id)
            ->whereIn('status', self::PENDING_CASHOUT_STATUSES)
            ->sum('amount');

        return round((float) $total, 2);
    }

    public static function available(User $trainer): float
    {
        $available = self::totalEarned($trainer) - self::withdrawn($trainer) - self::pending($trainer);

        return round(max(0, $available), 2);
    }

    /**
     * @return array{total: float, withdrawn: float, pending: float, available: float}
     */
    public static function summary(User $trainer): array
    {
        $total = self::totalEarned($trainer);
        $withdrawn = self::withdrawn($trainer);
        $pending = self::pending($trainer);
        $available = round(max(0, $total - $withdrawn - $pending), 2);

        return [
            'total' => $total,
            'withdrawn' => $withdrawn,
            'pending' => $pending,
            'available' => $available,
        ];
    }
}
