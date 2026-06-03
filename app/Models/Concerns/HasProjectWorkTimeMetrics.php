<?php

namespace App\Models\Concerns;

use App\Support\DurationFormatter;
use App\Support\ProjectWorkTime;

trait HasProjectWorkTimeMetrics
{
    public function getSpentTrackedSecondsAttribute(): int
    {
        return ProjectWorkTime::spentSeconds($this);
    }

    public function getExpectedWorkSecondsAttribute(): int
    {
        return ProjectWorkTime::expectedSeconds($this);
    }

    public function getRemainingWorkSecondsAttribute(): int
    {
        return ProjectWorkTime::remainingSeconds($this);
    }

    public function getSpentTimeLabelAttribute(): string
    {
        return DurationFormatter::format($this->spent_tracked_seconds);
    }

    public function getRemainingTimeLabelAttribute(): string
    {
        return DurationFormatter::format($this->remaining_work_seconds);
    }

    public function getExpectedTimeLabelAttribute(): string
    {
        return DurationFormatter::format($this->expected_work_seconds);
    }

    public function getProgressPercentageAttribute(): float
    {
        return ProjectWorkTime::progressPercent($this);
    }

    /** @return array<int, array{user_id: int|null, user_name: string, seconds: int, label: string}> */
    public function getSpentByWorkersAttribute(): array
    {
        return ProjectWorkTime::spentByWorkers($this);
    }

    public function getRemainingHoursAttribute(): float
    {
        return round($this->remaining_work_seconds / 3600, 1);
    }

    public function getSpentHoursAttribute(): float
    {
        return round($this->spent_tracked_seconds / 3600, 1);
    }
}
