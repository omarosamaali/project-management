<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseChatMessage extends Model
{
    protected $fillable = [
        'course_id',
        'user_id',
        'body',
        'is_hidden',
        'hidden_by',
        'hidden_at',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
        'hidden_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hiddenByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hidden_by');
    }
}
