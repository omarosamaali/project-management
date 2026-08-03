<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Support\CourseProfitSplitter;
use Illuminate\Console\Command;

class BackfillCoursePaymentProfits extends Command
{
    protected $signature = 'payments:backfill-course-profits {--chunk=200}';

    protected $description = 'Backfill trainer/platform profit snapshots on successful course payments';

    public function handle(): int
    {
        $statuses = ['completed', 'success', 'paid', 'active'];
        $chunk = max(50, (int) $this->option('chunk'));
        $updated = 0;

        Payment::query()
            ->whereNotNull('course_id')
            ->whereIn('status', $statuses)
            ->where(function ($q) {
                $q->whereNull('trainer_profit_amount')
                    ->orWhereNull('platform_profit_amount');
            })
            ->with('course')
            ->orderBy('id')
            ->chunkById($chunk, function ($payments) use (&$updated) {
                foreach ($payments as $payment) {
                    CourseProfitSplitter::applyToPayment($payment, $payment->course);
                    $updated++;
                }
            });

        $this->info("Backfilled profit snapshots on {$updated} payment(s).");

        return self::SUCCESS;
    }
}
