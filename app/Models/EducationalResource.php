<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalResource extends Model
{
    protected $fillable = ['title', 'language', 'users', 'youtube_url', 'status'];

    protected $casts = [
        'status' => 'boolean',
        'users' => 'array',
    ];
}
