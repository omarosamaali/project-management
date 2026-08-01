<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoursePathItem extends Model
{
    protected $fillable = [
        'unit_id',
        'type',
        'title_ar',
        'title_en',
        'video_path',
        'video_thumbnail_path',
        'video_embed_url',
        'video_duration_seconds',
        'exam_pass_score',
        'exam_duration_minutes',
        'sort_order',
    ];

    protected $casts = [
        'video_duration_seconds' => 'integer',
        'exam_pass_score' => 'integer',
        'exam_duration_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class, 'unit_id');
    }

    public function examQuestions(): HasMany
    {
        return $this->hasMany(CoursePathExamQuestion::class, 'path_item_id')->orderBy('sort_order');
    }

    public function isLesson(): bool
    {
        return $this->type === 'lesson';
    }

    public function isExam(): bool
    {
        return $this->type === 'exam';
    }

    public function localizedTitle(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        if ($locale === 'en') {
            return (string) ($this->title_en ?: $this->title_ar ?: '');
        }

        return (string) ($this->title_ar ?: $this->title_en ?: '');
    }

    public function videoUrl(): ?string
    {
        if (! $this->video_path) {
            return null;
        }

        // Never expose /storage/… — only short-lived signed stream URLs.
        $this->loadMissing('unit');
        $courseId = $this->unit?->course_id;
        if (! $courseId || ! auth()->check()) {
            return null;
        }

        return \App\Http\Controllers\CoursePathController::signedStreamUrl($courseId, $this->id);
    }

    public function thumbnailUrl(): ?string
    {
        if ($this->video_thumbnail_path) {
            return '/storage/' . ltrim(str_replace('\\', '/', $this->video_thumbnail_path), '/');
        }

        return null;
    }

    /**
     * Poster for the trainee player: custom/video-frame upload → platform thumbnail.
     * Never falls back to the course main image.
     */
    public function posterUrl(): ?string
    {
        if ($custom = $this->thumbnailUrl()) {
            return $custom;
        }

        if ($this->video_embed_url && !$this->video_path) {
            return \App\Support\LessonVideoSource::thumbnailUrl($this->video_embed_url);
        }

        return null;
    }

    public function hasPlayableVideo(): bool
    {
        return (bool) $this->playbackSource();
    }

    /**
     * Preferred playback source for the unified player.
     * Uploaded file wins over embed URL when both exist.
     *
     * @return array{provider: string, src: string, embed_id?: string, poster?: string}|null
     */
    public function playbackSource(): ?array
    {
        $poster = $this->posterUrl();

        if ($this->video_path) {
            $url = $this->videoUrl();
            if ($url) {
                $source = [
                    'provider' => \App\Support\LessonVideoSource::PROVIDER_HTML5,
                    'src' => $url,
                ];
                if ($poster) {
                    $source['poster'] = $poster;
                }

                return $source;
            }
        }

        $source = \App\Support\LessonVideoSource::fromUrl($this->video_embed_url);
        if ($source && $poster) {
            $source['poster'] = $poster;
        }

        return $source;
    }

    public function formattedDuration(): string
    {
        $seconds = (int) ($this->video_duration_seconds ?? 0);
        if ($seconds <= 0) {
            return '—';
        }

        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;

        if ($h > 0) {
            return sprintf('%d:%02d:%02d', $h, $m, $s);
        }

        return sprintf('%d:%02d', $m, $s);
    }

    /**
     * Exam time limit as clock-style duration (minutes → m:ss / h:mm:ss).
     */
    public function formattedExamDuration(): string
    {
        $minutes = max(0, (int) ($this->exam_duration_minutes ?? 0));
        if ($minutes <= 0) {
            return '—';
        }

        $seconds = $minutes * 60;
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;

        if ($h > 0) {
            return sprintf('%d:%02d:%02d', $h, $m, $s);
        }

        return sprintf('%d:%02d', $m, $s);
    }
}
