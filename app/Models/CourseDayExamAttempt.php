<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseDayExamAttempt extends Model
{
    protected $fillable = [
        'course_day_exam_id',
        'course_id',
        'user_id',
        'payment_id',
        'score',
        'passed',
        'answers',
        'shuffle_map',
        'submitted_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'passed' => 'boolean',
        'answers' => 'array',
        'shuffle_map' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function dayExam(): BelongsTo
    {
        return $this->belongsTo(CourseDayExam::class, 'course_day_exam_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }
}
