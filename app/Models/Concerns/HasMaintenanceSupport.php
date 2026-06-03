<?php

namespace App\Models\Concerns;

use Carbon\Carbon;

trait HasMaintenanceSupport
{
    public function usesProjectMaintenance(): bool
    {
        return $this->delivered_at
            && (int) ($this->maintenance_period ?? 0) > 0;
    }

    public function getMaintenanceEndDate(): ?Carbon
    {
        if (!$this->usesProjectMaintenance()) {
            return null;
        }

        $start = $this->delivered_at instanceof Carbon
            ? $this->delivered_at->copy()
            : Carbon::parse($this->delivered_at);

        if ($this->maintenance_unit === 'months') {
            return $start->addMonths((int) $this->maintenance_period);
        }

        return $start->addDays((int) $this->maintenance_period);
    }

    public function getMaintenanceTotalDaysAttribute(): int
    {
        if (!$this->usesProjectMaintenance()) {
            return 0;
        }

        $start = $this->support_start_date;
        $end   = $this->getMaintenanceEndDate();

        if (!$start || !$end) {
            return 0;
        }

        return max(1, (int) $start->copy()->startOfDay()->diffInDays($end->copy()->startOfDay(), false));
    }

    public function getMaintenanceRemainingDaysAttribute(): ?int
    {
        $end = $this->getMaintenanceEndDate();
        if (!$end) {
            return null;
        }

        if (now()->startOfDay()->gt($end->copy()->startOfDay())) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($end->copy()->startOfDay(), false);
    }

    public function getHasActiveMaintenanceAttribute(): bool
    {
        $remaining = $this->maintenance_remaining_days;

        return $remaining !== null && $remaining > 0;
    }

    public function getMaintenancePercentageAttribute(): int
    {
        $total = $this->maintenance_total_days;
        $remaining = $this->maintenance_remaining_days;

        if ($total <= 0 || $remaining === null || $remaining <= 0) {
            return 0;
        }

        return (int) min(100, ($remaining / $total) * 100);
    }

    public function getMaintenanceColorAttribute(): string
    {
        $remaining = $this->maintenance_remaining_days;
        $total     = $this->maintenance_total_days;

        if ($remaining === null || $remaining <= 0 || $total <= 0) {
            return 'gray';
        }

        $pct = ($remaining / $total) * 100;

        if ($pct > 50) {
            return 'green';
        }
        if ($pct > 20) {
            return 'yellow';
        }

        return 'red';
    }
}
