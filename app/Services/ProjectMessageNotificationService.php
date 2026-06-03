<?php

namespace App\Services;

use App\Models\Requests;
use App\Models\SpecialRequest;
use Illuminate\Support\Facades\Log;

class ProjectMessageNotificationService
{
    public function __construct(private WhatsAppOTPService $whatsapp) {}

    public function notifySpecialRequest(SpecialRequest $project, int $senderId, string $senderName, string $preview): void
    {
        $this->notifyPartners($project, $senderId, $senderName, $preview, $project->title);
    }

    public function notifyRequest(Requests $project, int $senderId, string $senderName, string $preview): void
    {
        $title = $project->title ?? "طلب #{$project->id}";
        $this->notifyPartners($project, $senderId, $senderName, $preview, $title);
    }

    private function notifyPartners(object $project, int $senderId, string $senderName, string $preview, string $title): void
    {
        try {
            $eventText = "رسالة جديدة من {$senderName}: \"{$preview}\"";
            foreach ($project->partners()->get() as $member) {
                if ($member->phone && (int) $member->id !== $senderId) {
                    $this->whatsapp->sendProjectNotification(
                        $member->phone,
                        $member->name,
                        $eventText,
                        $title
                    );
                }
            }
            $this->whatsapp->notifyManager($eventText, $title);
        } catch (\Exception $e) {
            Log::error('[MSG_NOTIFY] '.$e->getMessage());
        }
    }
}
