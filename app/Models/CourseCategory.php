<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseCategory extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'course_category_id');
    }

    public function title(?string $locale = null): string
    {
        $locale = strtolower((string) ($locale ?: app()->getLocale()));
        $isEnglish = str_starts_with($locale, 'en');

        if ($isEnglish) {
            return filled($this->title_en) ? (string) $this->title_en : (string) ($this->title_ar ?? '');
        }

        return filled($this->title_ar) ? (string) $this->title_ar : (string) ($this->title_en ?? '');
    }

    public function iconUrl(): string
    {
        if ($this->icon) {
            return asset('storage/' . ltrim($this->icon, '/'));
        }

        return Setting::academyLogoUrl();
    }

    public function hasCustomIcon(): bool
    {
        return filled($this->icon);
    }
}
