<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseRating extends Model
{
    protected $fillable = [
        'course_id',
        'user_id',
        'payment_id',
        'answers',
        'completed_at',
        'is_featured',
    ];

    protected $casts = [
        'answers' => 'array',
        'completed_at' => 'datetime',
        'is_featured' => 'boolean',
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

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    /**
     * Get the questions config for this rating's course type.
     */
    public function questionsConfig(): array
    {
        $type = $this->course?->location_type ?? 'on_site';

        return config("course_rating.{$type}", []);
    }

    /**
     * Average of all scale questions for a given target (trainer|course|academy).
     */
    public function scoreByTarget(string $target): ?float
    {
        $answers = $this->answers ?? [];
        $values = [];

        foreach ($this->questionsConfig() as $q) {
            if (($q['type'] ?? '') !== 'scale') {
                continue;
            }
            if (($q['target'] ?? '') !== $target) {
                continue;
            }
            $id = $q['id'] ?? null;
            if ($id && isset($answers[$id]) && is_numeric($answers[$id])) {
                $values[] = (float) $answers[$id];
            }
        }

        // Legacy single-field answers from the old survey format
        if (empty($values)) {
            $legacy = [
                'trainer' => 'trainer',
                'course' => 'overall',
                'academy' => null,
            ];
            $legacyKey = $legacy[$target] ?? null;
            if ($legacyKey && isset($answers[$legacyKey]) && is_numeric($answers[$legacyKey])) {
                $values[] = (float) $answers[$legacyKey];
            }
            if ($target === 'course' && isset($answers['content']) && is_numeric($answers['content'])) {
                $values[] = (float) $answers['content'];
            }
        }

        return empty($values) ? null : round(array_sum($values) / count($values), 1);
    }

    public function trainerScore(): ?float
    {
        return $this->scoreByTarget('trainer');
    }

    public function courseScore(): ?float
    {
        return $this->scoreByTarget('course');
    }

    public function academyScore(): ?float
    {
        return $this->scoreByTarget('academy');
    }

    /**
     * Overall average of every scale question regardless of target.
     */
    public function overallScore(): ?float
    {
        return $this->averageScaleScore();
    }

    public function averageScaleScore(): ?float
    {
        $answers = $this->answers ?? [];
        $scales = [];

        foreach ($this->questionsConfig() as $q) {
            if (($q['type'] ?? '') !== 'scale') {
                continue;
            }
            $id = $q['id'] ?? null;
            if ($id && isset($answers[$id]) && is_numeric($answers[$id])) {
                $scales[] = (float) $answers[$id];
            }
        }

        // Fallback: any numeric answers (covers legacy overall/content/trainer keys)
        if (empty($scales)) {
            foreach ($answers as $value) {
                if (is_numeric($value)) {
                    $scales[] = (float) $value;
                }
            }
        }

        return empty($scales) ? null : round(array_sum($scales) / count($scales), 1);
    }

    public function feedbackText(): ?string
    {
        $answers = $this->answers ?? [];
        $parts = [];
        foreach (['best_part', 'suggestions', 'feedback'] as $key) {
            $v = trim((string) ($answers[$key] ?? ''));
            if ($v !== '') {
                $parts[] = $v;
            }
        }

        return empty($parts) ? null : implode("\n\n", $parts);
    }

    public function recommendsCourse(): ?bool
    {
        $val = ($this->answers ?? [])['recommend'] ?? null;
        if ($val === null) {
            return null;
        }

        return in_array($val, [true, 1, '1', 'yes', 'نعم'], true);
    }
}
