<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoursePathLessonLink extends Model
{
    protected $fillable = [
        'path_item_id',
        'title_ar',
        'title_en',
        'url',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function pathItem(): BelongsTo
    {
        return $this->belongsTo(CoursePathItem::class, 'path_item_id');
    }

    /** @deprecated use pathItem() */
    public function lesson(): BelongsTo
    {
        return $this->pathItem();
    }

    public function title(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        if ($locale === 'en' && filled($this->title_en)) {
            return $this->title_en;
        }

        return $this->title_ar ?: (string) $this->title_en;
    }
}
