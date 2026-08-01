<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoursePathProgress extends Model
{
    protected $table = 'course_path_progress';

    protected $fillable = [
        'course_id',
        'user_id',
        'path_item_id',
        'video_watched_seconds',
        'video_played_seconds',
        'is_completed',
        'exam_score',
        'exam_passed',
        'exam_answers',
        'exam_time_spent_seconds',
        'shuffle_map',
        'completed_at',
    ];

    protected $casts = [
        'video_watched_seconds' => 'integer',
        'video_played_seconds' => 'integer',
        'is_completed' => 'boolean',
        'exam_score' => 'integer',
        'exam_passed' => 'boolean',
        'exam_answers' => 'array',
        'exam_time_spent_seconds' => 'integer',
        'shuffle_map' => 'array',
        'completed_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pathItem(): BelongsTo
    {
        return $this->belongsTo(CoursePathItem::class, 'path_item_id');
    }
}
