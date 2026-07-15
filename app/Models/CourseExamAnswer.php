<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseExamAnswer extends Model
{
    protected $fillable = [
        'question_id',
        'answer',
        'is_correct',
        'sort_order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(CourseExamQuestion::class, 'question_id');
    }
}
