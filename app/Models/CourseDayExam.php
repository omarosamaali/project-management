<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseDayExam extends Model
{
    protected $fillable = [
        'course_id',
        'day_index',
        'sort_order',
        'title',
        'pass_score',
        'duration_minutes',
        'started_at',
        'ended_at',
        'skipped_at',
        'skipped_by',
    ];

    protected $casts = [
        'day_index' => 'integer',
        'sort_order' => 'integer',
        'pass_score' => 'integer',
        'duration_minutes' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'skipped_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(CourseDayExamQuestion::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(CourseDayExamAttempt::class);
    }

    public function skippedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'skipped_by');
    }

    public function isRunning(): bool
    {
        return $this->started_at !== null && $this->ended_at === null && $this->skipped_at === null;
    }

    public function isSkipped(): bool
    {
        return $this->skipped_at !== null;
    }

    public function isFinished(): bool
    {
        return $this->ended_at !== null || $this->isSkipped();
    }

    public function isPending(): bool
    {
        return $this->started_at === null && $this->ended_at === null && $this->skipped_at === null;
    }

    public function status(): string
    {
        if ($this->isSkipped()) {
            return 'skipped';
        }
        if ($this->ended_at) {
            return 'finished';
        }
        if ($this->started_at) {
            return 'running';
        }

        return 'not_started';
    }

    public function statusLabel(): string
    {
        return match ($this->status()) {
            'not_started' => 'لم يبدأ',
            'running' => 'جارٍ',
            'finished' => 'منتهٍ',
            'skipped' => 'متخطى',
            default => '—',
        };
    }

    public function displayTitle(): string
    {
        $title = trim((string) ($this->title ?? ''));
        if ($title !== '') {
            return $title;
        }

        return 'اختبار اليوم ' . $this->day_index;
    }
}
