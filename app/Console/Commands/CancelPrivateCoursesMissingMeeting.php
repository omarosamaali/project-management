<?php

namespace App\Console\Commands;

use App\Support\PrivateCourseRequestService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelPrivateCoursesMissingMeeting extends Command
{
    protected $signature = 'private-courses:cancel-missing-meetings';

    protected $description = 'Cancel paid private courses that started without a meeting link';

    public function handle(PrivateCourseRequestService $service): int
    {
        $canceled = $service->cancelMissingMeetingLinks();

        $this->info("Canceled for missing meeting link: {$canceled}.");

        Log::info('[PRIVATE_COURSE] cancel-missing-meetings finished', [
            'canceled' => $canceled,
        ]);

        return self::SUCCESS;
    }
}
