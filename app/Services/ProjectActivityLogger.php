<?php

namespace App\Services;

use App\Models\ProjectActivity;
use App\Models\ProjectStage;
use App\Models\RequestActivity;
use App\Models\RequestStage;
use App\Models\Requests;
use App\Models\SpecialRequest;
use App\Models\Task;
use App\Models\User;
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

    public function logTaskCompleted(Task $task, ?int $actorUserId = null): void
    {
        $actor = $this->resolveActor($actorUserId ?? $task->user_id);
        $actorName = $actor?->name ?? 'مستخدم';

        $description = "تم إنجاز المهمة: «{$task->title}» بواسطة {$actorName}";

        if ($task->special_request_id) {
            ProjectActivity::create([
                'special_request_id' => $task->special_request_id,
                'user_id'            => $actor?->id ?? $task->user_id,
                'type'               => 'task_completed',
                'description'        => $description,
                'properties'         => [
                    'task_id'    => $task->id,
                    'task_title' => $task->title,
                ],
            ]);

            $project = SpecialRequest::find($task->special_request_id);
            if ($project) {
                $this->notifyProjectTeam("{$description}", $project->title ?? "مشروع #{$project->id}", $project);
            }
        }

        if ($task->request_id) {
            RequestActivity::create([
                'request_id'  => $task->request_id,
                'user_id'     => $actor?->id ?? $task->user_id,
                'type'        => 'task_completed',
                'description' => $description,
                'properties'  => [
                    'task_id'    => $task->id,
                    'task_title' => $task->title,
                ],
            ]);

            $project = Requests::with('system')->find($task->request_id);
            if ($project) {
                $title = $project->system?->name_ar ?? "طلب #{$project->id}";
                $this->notifyProjectTeam($description, $title, $project);
            }
        }
    }

    public function logStageCompleted(ProjectStage|RequestStage $stage, ?int $actorUserId = null): void
    {
        $actor = $this->resolveActor($actorUserId);
        $actorName = $actor?->name ?? 'مستخدم';
        $description = "تم إنجاز المرحلة: «{$stage->title}» بواسطة {$actorName}";

        if ($stage instanceof ProjectStage && $stage->special_request_id) {
            ProjectActivity::create([
                'special_request_id' => $stage->special_request_id,
                'user_id'            => $actor?->id ?? auth()->id(),
                'type'               => 'stage_completed',
                'description'        => $description,
                'properties'         => [
                    'stage_id'    => $stage->id,
                    'stage_title' => $stage->title,
                ],
            ]);

            $project = SpecialRequest::find($stage->special_request_id);
            if ($project) {
                $this->notifyProjectTeam($description, $project->title ?? "مشروع #{$project->id}", $project);
            }

            return;
        }

        if ($stage instanceof RequestStage && $stage->request_id) {
            RequestActivity::create([
                'request_id'  => $stage->request_id,
                'user_id'     => $actor?->id ?? auth()->id(),
                'type'        => 'stage_completed',
                'description' => $description,
                'properties'  => [
                    'stage_id'    => $stage->id,
                    'stage_title' => $stage->title,
                ],
            ]);

            $project = Requests::with('system')->find($stage->request_id);
            if ($project) {
                $title = $project->system?->name_ar ?? "طلب #{$project->id}";
                $this->notifyProjectTeam($description, $title, $project);
            }
        }
    }

    private function resolveActor(?int $userId): ?User
    {
        if ($userId) {
            return User::find($userId);
        }

        return auth()->user();
    }

    private function notifyProjectTeam(string $eventText, string $projectTitle, SpecialRequest|Requests $project): void
    {
        try {
            $whatsapp = app(WhatsAppOTPService::class);
            $whatsapp->notifyManager($eventText, $projectTitle);

            $notifiedPhones = [];

            foreach ($project->partners()->get() as $partner) {
                if (empty($partner->phone) || isset($notifiedPhones[$partner->phone])) {
                    continue;
                }
                $notifiedPhones[$partner->phone] = true;
                $whatsapp->sendProjectNotification(
                    $partner->phone,
                    $partner->name,
                    $eventText,
                    $projectTitle,
                    $partner->email ?? null
                );
            }

            if (method_exists($project, 'allProjectClients')) {
                foreach ($project->allProjectClients() as $client) {
                    if (empty($client->phone) || isset($notifiedPhones[$client->phone])) {
                        continue;
                    }
                    $notifiedPhones[$client->phone] = true;
                    $whatsapp->sendProjectNotification(
                        $client->phone,
                        $client->name,
                        $eventText,
                        $projectTitle,
                        $client->email ?? null
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('[ACTIVITY_NOTIFY] ' . $e->getMessage(), [
                'project' => $projectTitle,
                'event'   => $eventText,
            ]);
        }
    }
}
