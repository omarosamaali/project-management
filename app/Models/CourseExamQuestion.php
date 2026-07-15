<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseExamQuestion extends Model
{
    protected $fillable = [
        'course_id',
        'question',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CourseExamAnswer::class, 'question_id')->orderBy('sort_order');
    }
}
