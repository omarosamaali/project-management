<?php

namespace App\Support;

use App\Models\Requests;
use App\Models\SpecialRequest;
use App\Models\User;
use Illuminate\Support\Collection;

class SystemManager
{
    public static function displayName(): string
    {
        return (string) config('system.manager_display_name', 'طارق بن كلبان');
    }

    public static function userId(): ?int
    {
        $configured = config('system.manager_user_id');

        if ($configured) {
            return (int) $configured;
        }

        $id = User::where('role', 'admin')->orderBy('id')->value('id');

        return $id ? (int) $id : null;
    }

    public static function user(): ?User
    {
        $id = self::userId();

        return $id ? User::find($id) : null;
    }

    public static function is(?User $user): bool
    {
        if (!$user || !self::userId()) {
            return false;
        }

        return (int) $user->id === (int) self::userId();
    }

    public static function nameFor(?User $user): string
    {
        if (!$user) {
            return '';
        }

        return self::is($user) ? self::displayName() : (string) $user->name;
    }

    /**
     * من يمكن إسناد المهام إليهم: طارق/الأدمن + شركاء المشروع + مدير المشروع.
     */
    public static function assignableUsers(SpecialRequest|Requests $project): Collection
    {
        $users = collect();

        User::where('role', 'admin')->orderBy('id')->get()->each(function (User $admin) use ($users) {
            if (!$users->contains('id', $admin->id)) {
                $users->push($admin);
            }
        });

        $partners = $project->relationLoaded('partners')
            ? $project->partners
            : $project->partners()->get();

        foreach ($partners as $partner) {
            if ($partner->isBlocked()) {
                continue;
            }
            if (!$users->contains('id', $partner->id)) {
                $users->push($partner);
            }
        }

        $pmUser = $project->projectManager?->user;
        if ($pmUser && !$pmUser->isBlocked() && !$users->contains('id', $pmUser->id)) {
            $users->push($pmUser);
        }

        return $users->sortBy(function (User $user) {
            if (self::is($user)) {
                return '0';
            }

            return $user->name;
        })->values();
    }

    public static function addToAttendeeCollection(Collection $attendees): Collection
    {
        if ($manager = self::user()) {
            $attendees->push($manager);
        }

        User::where('role', 'admin')->get()->each(function (User $admin) use ($attendees) {
            if (!$attendees->contains('id', $admin->id)) {
                $attendees->push($admin);
            }
        });

        return $attendees->unique('id')->values();
    }
}
