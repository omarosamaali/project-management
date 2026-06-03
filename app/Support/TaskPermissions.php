<?php

namespace App\Support;

use App\Models\Project_Manager;
use App\Models\Requests;
use App\Models\SpecialRequest;
use App\Models\Task;
use App\Models\User;

class TaskPermissions
{
    /** أدمن + مدير المشروع + شركاء المشروع (فريق العمل) */
    public static function canManage(User $user, SpecialRequest|Requests $project): bool
    {
        if ($user->role === 'client') {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        if (self::isProjectManager($user, $project)) {
            return true;
        }

        if (in_array($user->role, ['partner', 'independent_partner'], true)) {
            return self::isProjectPartner($user, $project);
        }

        return false;
    }

    /** تشغيل المؤقت (ابدأ / توقف / إنهاء): مسؤول المهمة فقط */
    public static function canTrack(User $user, Task $task, SpecialRequest|Requests $project): bool
    {
        if ($user->role === 'client') {
            return false;
        }

        if (!$task->user_id) {
            return false;
        }

        return (int) $task->user_id === (int) $user->id;
    }

    public static function resolveProjectFromTask(Task $task): SpecialRequest|Requests|null
    {
        if ($task->special_request_id) {
            return SpecialRequest::find($task->special_request_id);
        }

        if ($task->request_id) {
            return Requests::find($task->request_id);
        }

        return null;
    }

    public static function authorizeManage(User $user, Task $task): void
    {
        $project = self::resolveProjectFromTask($task);

        if (!$project || !self::canManage($user, $project)) {
            abort(403, 'غير مسموح لك بإدارة المهام في هذا المشروع.');
        }
    }

    public static function authorizeManageProject(User $user, SpecialRequest|Requests $project): void
    {
        if (!self::canManage($user, $project)) {
            abort(403, 'غير مسموح لك بإدارة المهام في هذا المشروع.');
        }
    }

    public static function authorizeTrack(User $user, Task $task): void
    {
        $project = self::resolveProjectFromTask($task);

        if (!$project || !self::canTrack($user, $task, $project)) {
            abort(403, 'عداد المهمة متاح لمسؤول المهمة فقط.');
        }
    }

    private static function isProjectManager(User $user, SpecialRequest|Requests $project): bool
    {
        if ($project instanceof SpecialRequest) {
            return Project_Manager::where('user_id', $user->id)
                ->where('special_request_id', $project->id)
                ->exists();
        }

        return Project_Manager::where('user_id', $user->id)
            ->where('request_id', $project->id)
            ->exists();
    }

    private static function isProjectPartner(User $user, SpecialRequest|Requests $project): bool
    {
        return $project->partners()->where('users.id', $user->id)->exists();
    }
}
