<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseExamAttempt extends Model
{
    protected $fillable = [
        'course_id',
        'user_id',
        'payment_id',
        'score',
        'passed',
        'answers',
        'submitted_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'passed' => 'boolean',
        'score' => 'integer',
        'submitted_at' => 'datetime',
    ];

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
