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
        'request_stage_id', // أضف هذا العمود لأنه موجود في جدولك
        'user_id',
        'title',
        'details',
        'start_date',
        'end_date',
        'status'
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
}
