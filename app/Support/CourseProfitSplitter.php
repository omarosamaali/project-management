<?php

namespace App\Support;

use App\Models\Course;
use App\Models\Payment;
use App\Models\Setting;

class CourseProfitSplitter
{
    /**
     * Compute and persist trainer/platform profit snapshot on a successful course payment.
     */
    public static function applyToPayment(Payment $payment, ?Course $course = null): void
    {
        $course = $course ?? $payment->course;
        if (! $course) {
            $payment->forceFill([
                'trainer_profit_pct' => 0,
                'trainer_profit_amount' => 0,
                'platform_profit_amount' => (float) ($payment->original_price ?? 0),
            ])->save();

            return;
        }

        $base = round((float) ($payment->original_price ?? 0), 2);
        $hasTrainer = filled($course->trainer_id);
        $pct = $hasTrainer
            ? Setting::academyTrainerProfitPercentageFor((string) $course->location_type)
            : 0.0;

        $trainerAmount = $hasTrainer
            ? round($base * ($pct / 100), 2)
            : 0.0;
        $platformAmount = round($base - $trainerAmount, 2);

        $payment->forceFill([
            'trainer_profit_pct' => $pct,
            'trainer_profit_amount' => $trainerAmount,
            'platform_profit_amount' => $platformAmount,
        ])->save();
    }
}
