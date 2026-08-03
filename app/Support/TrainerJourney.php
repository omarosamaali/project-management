<?php

namespace App\Support;

use App\Models\User;

class TrainerJourney
{
    public const STORAGE_KEY = 'evorq_trainer_journey_v1';

    public const TOTAL_STEPS = 5;

    /**
     * Current journey step (1–5) for a trainer account.
     *
     * 1 Apply · 2 Admin review · 3 Approval · 4 Create course · 5 Publish course
     */
    public static function stepFor(?User $user): int
    {
        if (! $user || ! $user->isTrainer()) {
            return 1;
        }

        if ($user->isPendingApproval() || $user->status === 'pending') {
            return 2;
        }

        if ($user->status !== 'active' || $user->isBlocked()) {
            return 1;
        }

        $hasPublished = $user->trainedCourses()
            ->where('status', 'active')
            ->exists();

        if ($hasPublished) {
            return 5;
        }

        $hasAnyCourse = $user->trainedCourses()->exists();

        // Approved: create course is next (approval step is completed).
        return $hasAnyCourse ? 5 : 4;
    }

    /**
     * @return array{step:int,completed:int,all_done:bool}
     */
    public static function stateFor(?User $user): array
    {
        $step = self::stepFor($user);
        $allDone = $user
            && $user->isTrainer()
            && $user->status === 'active'
            && ! $user->isBlocked()
            && $user->trainedCourses()->where('status', 'active')->exists();

        if ($allDone) {
            return [
                'step' => self::TOTAL_STEPS,
                'completed' => self::TOTAL_STEPS,
                'all_done' => true,
            ];
        }

        return [
            'step' => $step,
            'completed' => max(0, $step - 1),
            'all_done' => false,
        ];
    }

    public static function hintFor(int $step, bool $allDone = false): string
    {
        if ($allDone) {
            return __('messages.become_trainer_journey_hint_done');
        }

        return match ($step) {
            2 => __('messages.become_trainer_journey_hint_review'),
            3 => __('messages.become_trainer_journey_hint_approve'),
            4 => __('messages.become_trainer_journey_hint_create'),
            5 => __('messages.become_trainer_journey_hint_publish'),
            default => __('messages.become_trainer_journey_hint'),
        };
    }

    /**
     * Destination URL for a journey step when that step is available to open.
     */
    public static function urlForStep(int $step, ?User $user = null): ?string
    {
        $user = $user ?? auth()->user();
        $isActiveTrainer = $user
            && $user->isTrainer()
            && $user->status === 'active'
            && ! $user->isBlocked();

        return match ($step) {
            1 => route('academy.become-trainer').'#trainer-application',
            2, 3 => $user && $user->isTrainer()
                ? route('profile.edit')
                : route('academy.become-trainer'),
            4 => $isActiveTrainer
                ? route('dashboard.courses.create')
                : null,
            5 => $isActiveTrainer
                ? route('dashboard.courses.index')
                : null,
            default => null,
        };
    }

    /**
     * Whether a step can be opened (done / active / completed journey).
     */
    public static function stepIsAvailable(int $step, int $currentStep, bool $allDone = false): bool
    {
        if ($allDone) {
            return true;
        }

        return $step <= $currentStep;
    }
}
