<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    // في ملف App\Models\Task.php
    protected $fillable = [
        'special_request_id',
        'request_id',
        'project_stage_id',
        'request_stage_id',
        'user_id',
        'created_by',
        'title',
        'details',
        'start_date',
        'end_date',
        'status',
        'tracked_seconds',
        'timer_started_at',
        'is_timer_running',
    ];

    protected $casts = [
        'timer_started_at' => 'datetime',
        'is_timer_running' => 'boolean',
    ];

    // علاقة المهمة بالمشروع
    public function specialRequest(): BelongsTo
    {
        return $this->belongsTo(SpecialRequest::class);
    }

    // علاقة المهمة بالمستخدم (المسؤول عنها)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // علاقة المهمة بالمرحلة (إذا كانت تابعة لمرحلة معينة)
    public function stage(): BelongsTo
    {
        return $this->belongsTo(ProjectStage::class, 'project_stage_id');
    }
    public function stages()
    {
        return $this->hasMany(RequestStage::class, 'project_id');
    }
    public function request(): BelongsTo
    {
        return $this->belongsTo(Requests::class, 'request_id');
    }

    public function requestStage(): BelongsTo
    {
        return $this->belongsTo(RequestStage::class, 'request_stage_id');
    }

    /** الوقت المخزّن فقط (بعد إيقاف/حفظ العداد). */
    public function getStoredTrackedSecondsAttribute(): int
    {
        return max((int) ($this->tracked_seconds ?? 0), 0);
    }

    /** المخزّن + جلسة العداد الجارية (للعرض أثناء التشغيل). */
    public function getElapsedTrackedSecondsAttribute(): int
    {
        $seconds = $this->stored_tracked_seconds;

        if ($this->is_timer_running && $this->timer_started_at) {
            $seconds += (int) $this->timer_started_at->diffInSeconds(now());
        }

        return max($seconds, 0);
    }
}
