<?php

namespace App\Services;

use App\Models\Requests;
use App\Models\SpecialRequest;

class ProjectMessageNotificationService
{
    public function __construct(private ProjectActivityLogger $logger) {}

    public function notifySpecialRequest(SpecialRequest $project, int $senderId, string $senderName, string $preview): void
    {
        $description = 'رسالة جديدة في النقاشات من '.$senderName.': «'.$preview.'»';
        $this->logger->logSpecialRequest(
            $project,
            $description,
            'chat',
            $senderId,
            ['preview' => $preview],
            true,
            $senderId,
        );
    }

    public function notifyRequest(Requests $project, int $senderId, string $senderName, string $preview): void
    {
        $description = 'رسالة جديدة في النقاشات من '.$senderName.': «'.$preview.'»';
        $this->logger->logRequest(
            $project,
            $description,
            'chat',
            $senderId,
            ['preview' => $preview],
            true,
            $senderId,
        );
    }
}
