<?php
// app/Models/Support.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Support extends Model
{
    protected $fillable = [
        'request_id',
        'user_id',
        'subject',
        'status',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime'
    ];

    public function request()
    {
        return $this->belongsTo(Requests::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(SupportMessage::class)->orderBy('created_at', 'asc');
    }

    public function unreadMessages()
    {
        // 
        return $this->hasMany(SupportMessage::class)
            ->where('is_read', false)
            ->where('user_id', '!=', Auth::id());
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'open' => 'مفتوحة',
            'in_progress' => 'قيد المعالجة',
            'closed' => 'ملغية',
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}
