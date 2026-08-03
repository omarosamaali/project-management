<?php

namespace App\Console\Commands;

use App\Support\PrivateCourseRequestService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpirePrivateCourseRequests extends Command
{
    protected $signature = 'private-courses:expire-requests';

    protected $description = 'Expire unpaid private course requests and busy trainer timeouts';

    public function handle(PrivateCourseRequestService $service): int
    {
        $expiredUnpaid = $service->expireUnpaid();
        $expiredBusy = $service->expireBusy();

        $this->info("Expired unpaid: {$expiredUnpaid}, expired busy: {$expiredBusy}.");

        Log::info('[PRIVATE_COURSE] expire-requests finished', [
            'expired_unpaid' => $expiredUnpaid,
            'expired_busy' => $expiredBusy,
        ]);

        return self::SUCCESS;
    }
}
