<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\ProjectActivity;
use App\Models\ProjectStage;
use App\Models\RequestActivity;
use App\Models\RequestStage;
use App\Models\Requests;
use App\Models\SpecialRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProjectActivityLogger
{
    private const TASK_COMPLETED_STATUSES = ['منتهية', 'completed', 'done'];

    private const STAGE_COMPLETED_STATUSES = ['completed', 'منتهية', 'done'];

    public function isTaskCompleted(?string $status): bool
    {
        return in_array($status, self::TASK_COMPLETED_STATUSES, true);
    }

    public function isStageCompleted(?string $status): bool
    {
        return in_array($status, self::STAGE_COMPLETED_STATUSES, true);
    }

    /**
     * تسجيل نشاط في طلب خاص + إشعار الفريق (واتساب + بريد).
     */
    public function logSpecialRequest(
        SpecialRequest|int $project,
        string $description,
        string $type = 'activity',
        ?int $userId = null,
        ?array $properties = null,
        bool $notify = true,
        ?int $excludeUserId = null,
    ): void {
        $project = $project instanceof SpecialRequest ? $project : SpecialRequest::find($project);
        if (!$project) {
            return;
        }

        $actorId = $userId ?? auth()->id();

        ProjectActivity::create([
            'special_request_id' => $project->id,
            'user_id'            => $actorId,
            'type'               => $type,
            'description'        => $description,
            'properties'         => $properties,
        ]);

        if ($notify) {
            $this->notifyProjectTeam(
                $description,
                $project->title ?? "مشروع #{$project->id}",
                $project,
                $excludeUserId,
                $type,
            );
        }
    }

    /**
     * تسجيل نشاط في طلب نظام + إشعار الفريق.
     */
    public function logRequest(
        Requests|int $project,
        string $description,
        string $type = 'activity',
        ?int $userId = null,
        ?array $properties = null,
        bool $notify = true,
        ?int $excludeUserId = null,
    ): void {
        $project = $project instanceof Requests ? $project : Requests::with('system')->find($project);
        if (!$project) {
            return;
        }

        $actorId = $userId ?? auth()->id();
        $title = $project->system?->name_ar ?? $project->title ?? "طلب #{$project->id}";

        RequestActivity::create([
            'request_id'  => $project->id,
            'user_id'     => $actorId,
            'type'        => $type,
            'description' => $description,
            'properties'  => $properties,
        ]);

        if ($notify) {
            $this->notifyProjectTeam($description, $title, $project, $excludeUserId, $type);
        }
    }

    public function logTaskCompleted(Task $task, ?int $actorUserId = null): void
    {
        $actor = $this->resolveActor($actorUserId ?? $task->user_id);
        $actorName = $actor?->name ?? 'مستخدم';

        $description = "تم إنجاز المهمة: «{$task->title}» بواسطة {$actorName}";

        if ($task->special_request_id) {
            $this->logSpecialRequest(
                $task->special_request_id,
                $description,
                'task_completed',
                $actor?->id ?? $task->user_id,
                ['task_id' => $task->id, 'task_title' => $task->title],
            );
        }

        if ($task->request_id) {
            $this->logRequest(
                $task->request_id,
                $description,
                'task_completed',
                $actor?->id ?? $task->user_id,
                ['task_id' => $task->id, 'task_title' => $task->title],
            );
        }
    }

    public function logStageCompleted(ProjectStage|RequestStage $stage, ?int $actorUserId = null): void
    {
        $actor = $this->resolveActor($actorUserId);
        $actorName = $actor?->name ?? 'مستخدم';
        $description = "تم إنجاز المرحلة: «{$stage->title}» بواسطة {$actorName}";

        if ($stage instanceof ProjectStage && $stage->special_request_id) {
            $this->logSpecialRequest(
                $stage->special_request_id,
                $description,
                'stage_completed',
                $actor?->id ?? auth()->id(),
                ['stage_id' => $stage->id, 'stage_title' => $stage->title],
            );

            return;
        }

        if ($stage instanceof RequestStage && $stage->request_id) {
            $this->logRequest(
                $stage->request_id,
                $description,
                'stage_completed',
                $actor?->id ?? auth()->id(),
                ['stage_id' => $stage->id, 'stage_title' => $stage->title],
            );
        }
    }

    private function resolveActor(?int $userId): ?User
    {
        if ($userId) {
            return User::find($userId);
        }

        return auth()->user();
    }

    /**
     * @return Collection<int, User>
     */
    private function collectRecipients(SpecialRequest|Requests $project): Collection
    {
        $users = collect();

        if (method_exists($project, 'partners')) {
            $users = $users->merge($project->partners()->get());
        }

        if (method_exists($project, 'allProjectClients')) {
            $users = $users->merge($project->allProjectClients());
        } elseif (method_exists($project, 'clients')) {
            $users = $users->merge($project->clients()->get());
        }

        if ($project instanceof SpecialRequest) {
            if ($project->user_id) {
                $owner = User::find($project->user_id);
                if ($owner) {
                    $users->push($owner);
                }
            }
            $assigneeIds = Task::where('special_request_id', $project->id)
                ->whereNotNull('user_id')
                ->distinct()
                ->pluck('user_id');
        } else {
            $assigneeIds = Task::where('request_id', $project->id)
                ->whereNotNull('user_id')
                ->distinct()
                ->pluck('user_id');
        }

        if ($assigneeIds->isNotEmpty()) {
            $users = $users->merge(User::whereIn('id', $assigneeIds)->get());
        }

        return $users->filter()->unique('id')->values();
    }

    private function projectUrl(SpecialRequest|Requests $project): string
    {
        if ($project instanceof SpecialRequest) {
            return route('dashboard.special-request.show', $project->id);
        }

        return route('dashboard.requests.show', $project->id);
    }

    private function iconForType(string $type): string
    {
        return match ($type) {
            'chat' => 'fa-comments',
            'task', 'task_completed' => 'fa-tasks',
            'file' => 'fa-file',
            'approval' => 'fa-stamp',
            'issue' => 'fa-exclamation-triangle',
            'meeting' => 'fa-video',
            'expense' => 'fa-receipt',
            'note' => 'fa-sticky-note',
            'stage', 'stage_added', 'stage_completed' => 'fa-layer-group',
            'team' => 'fa-users',
            default => 'fa-bell',
        };
    }

    private function uiTypeFor(string $type): string
    {
        return match ($type) {
            'issue', 'task_completed', 'stage_completed' => 'success',
            'chat' => 'warning',
            default => 'info',
        };
    }

    private function notifyProjectTeam(
        string $eventText,
        string $projectTitle,
        SpecialRequest|Requests $project,
        ?int $excludeUserId = null,
        string $activityType = 'activity',
    ): void {
        try {
            $whatsapp = app(WhatsAppOTPService::class);
            $whatsapp->notifyManager($eventText, $projectTitle);

            $url = $this->projectUrl($project);
            $icon = $this->iconForType($activityType);
            $uiType = $this->uiTypeFor($activityType);
            $notifiedUserIds = [];

            $this->notifyManagementInApp($projectTitle, $eventText, $url, $icon, $uiType, $notifiedUserIds);

            $notifiedPhones = [];
            $notifiedEmails = [];

            foreach ($this->collectRecipients($project) as $member) {
                if ($excludeUserId && (int) $member->id === (int) $excludeUserId) {
                    continue;
                }

                $this->pushInAppNotification(
                    $member->id,
                    $projectTitle,
                    $eventText,
                    $url,
                    $icon,
                    $uiType,
                    $notifiedUserIds,
                );

                $phone = trim((string) ($member->phone ?? ''));
                $email = trim((string) ($member->email ?? ''));

                if ($phone !== '' && !isset($notifiedPhones[$phone])) {
                    $notifiedPhones[$phone] = true;
                    $whatsapp->sendProjectNotification(
                        $phone,
                        $member->name,
                        $eventText,
                        $projectTitle,
                        $email !== '' ? $email : null,
                    );
                    if ($email !== '') {
                        $notifiedEmails[$email] = true;
                    }
                    continue;
                }

                if ($email !== '' && !isset($notifiedEmails[$email])) {
                    $notifiedEmails[$email] = true;
                    $body = "{$eventText} في المشروع: {$projectTitle}";
                    $whatsapp->sendEmailNotification(
                        $email,
                        $member->name,
                        "إشعار مشروع: {$projectTitle}",
                        $body,
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('[ACTIVITY_NOTIFY] '.$e->getMessage(), [
                'project' => $projectTitle,
                'event'   => $eventText,
            ]);
        }
    }

    /**
     * @param  array<int, true>  $notifiedUserIds
     */
    private function pushInAppNotification(
        int $userId,
        string $title,
        string $message,
        string $url,
        string $icon,
        string $type,
        array &$notifiedUserIds,
    ): void {
        if (isset($notifiedUserIds[$userId])) {
            return;
        }

        $notifiedUserIds[$userId] = true;

        AppNotification::notify($userId, $title, $message, $url, $icon, $type);
    }

    /**
     * @param  array<int, true>  $notifiedUserIds
     */
    private function notifyManagementInApp(
        string $title,
        string $message,
        string $url,
        string $icon,
        string $type,
        array &$notifiedUserIds,
    ): void {
        $emails = array_values(array_filter([
            WhatsAppOTPService::MANAGER_EMAIL,
            WhatsAppOTPService::ADMIN_EMAIL,
        ]));

        if ($emails === []) {
            return;
        }

        User::whereIn('email', $emails)->get()->each(function (User $user) use (
            $title,
            $message,
            $url,
            $icon,
            $type,
            &$notifiedUserIds,
        ) {
            $this->pushInAppNotification($user->id, $title, $message, $url, $icon, $type, $notifiedUserIds);
        });
    }
}
