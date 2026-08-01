<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoursePathExamQuestion extends Model
{
    protected $fillable = [
        'path_item_id',
        'question',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function pathItem(): BelongsTo
    {
        return $this->belongsTo(CoursePathItem::class, 'path_item_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CoursePathExamAnswer::class, 'question_id')->orderBy('sort_order');
    }
}
