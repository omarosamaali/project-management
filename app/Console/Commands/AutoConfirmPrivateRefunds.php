<?php

namespace App\Console\Commands;

use App\Support\PrivateCourseRequestService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoConfirmPrivateRefunds extends Command
{
    protected $signature = 'private-courses:auto-confirm-refunds';

    protected $description = 'Auto-confirm private course refunds after trainee confirmation window';

    public function handle(PrivateCourseRequestService $service): int
    {
        $confirmed = $service->autoConfirmRefunds();

        $this->info("Auto-confirmed refunds: {$confirmed}.");

        Log::info('[PRIVATE_COURSE] auto-confirm-refunds finished', [
            'confirmed' => $confirmed,
        ]);

        return self::SUCCESS;
    }
}
