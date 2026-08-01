<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseUnit extends Model
{
    protected $fillable = [
        'course_id',
        'title_ar',
        'title_en',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CoursePathItem::class, 'unit_id')->orderBy('sort_order');
    }

    public function localizedTitle(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        if ($locale === 'en') {
            return (string) ($this->title_en ?: $this->title_ar ?: '');
        }

        return (string) ($this->title_ar ?: $this->title_en ?: '');
    }
}
