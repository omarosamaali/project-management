<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\TrainerCashoutRequest;
use App\Models\TrainerCashoutScreenshot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoConfirmTrainerCashouts extends Command
{
    protected $signature = 'trainer-cashouts:auto-confirm';

    protected $description = 'Auto-confirm trainer cashout requests after the trainer confirmation window (24h)';

    public function handle(): int
    {
        $cashouts = TrainerCashoutRequest::query()
            ->where('status', TrainerCashoutRequest::STATUS_PENDING_TRAINER_CONFIRM)
            ->whereNotNull('trainer_confirm_due_at')
            ->where('trainer_confirm_due_at', '<=', now())
            ->whereNull('trainer_confirmed_at')
            ->whereHas('screenshots', function ($q) {
                $q->where('kind', TrainerCashoutScreenshot::KIND_SUCCESS);
            })
            ->with('user')
            ->get();

        $count = 0;

        foreach ($cashouts as $cashout) {
            $cashout->update([
                'status' => TrainerCashoutRequest::STATUS_PAID,
                'trainer_confirmed_at' => now(),
            ]);

            $amount = number_format((float) $cashout->amount, 2);
            $currency = $cashout->currency ?: 'AED';

            if ($cashout->user) {
                try {
                    AppNotification::notify(
                        $cashout->user->id,
                        'تم تأكيد طلب السحب تلقائياً',
                        "تم إغلاق طلب سحب الأرباح تلقائياً بعد 24 ساعة بدون تأكيد. المبلغ: {$amount} {$currency}.",
                        route('dashboard.academy.my-profits'),
                        'fa-money-bill-wave',
                        'success'
                    );
                } catch (\Throwable $e) {
                    Log::warning('[TRAINER_CASHOUT] auto-confirm notify failed', [
                        'cashout_id' => $cashout->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $count++;
        }

        $this->info("Auto-confirmed trainer cashouts: {$count}.");

        Log::info('[TRAINER_CASHOUT] auto-confirm finished', [
            'confirmed' => $count,
        ]);

        return self::SUCCESS;
    }
}
