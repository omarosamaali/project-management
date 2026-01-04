<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestMeeting extends Model
{
    protected $fillable = ['request_id', 'created_by', 'title', 'meeting_link', 'start_at', 'end_at'];
    protected $casts = ['start_at' => 'datetime', 'end_at' => 'datetime'];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'request_meeting_participants', 'request_meeting_id', 'user_id')
            ->withPivot('status')->withTimestamps();
    }
}