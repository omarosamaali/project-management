<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateCourseRequestEvent extends Model
{
    protected $fillable = [
        'private_course_request_id',
        'actor_id',
        'action',
        'message',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function request()
    {
        return $this->belongsTo(PrivateCourseRequest::class, 'private_course_request_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
