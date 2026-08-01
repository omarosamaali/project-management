<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseDayExamQuestion extends Model
{
    protected $fillable = [
        'course_day_exam_id',
        'question',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function dayExam(): BelongsTo
    {
        return $this->belongsTo(CourseDayExam::class, 'course_day_exam_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CourseDayExamAnswer::class, 'question_id')->orderBy('sort_order');
    }
}
