<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = ['special_request_id', 'user_id', 'title', 'attendees', 'meeting_link', 'start_at', 'end_at'];

    // تحويل الحضور من JSON إلى مصفوفة تلقائياً
    protected $casts = [
        'attendees' => 'array',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function getStatusAttribute()
    {
        $now = now();
        if ($now->lt($this->start_at)) return 'بالانتظار';
        if ($now->between($this->start_at, $this->end_at)) return 'مفتوح';
        return 'منتهي';
    }

    public function getStatusColorAttribute()
    {
        return [
            'بالانتظار' => 'bg-amber-100 text-amber-700',
            'مفتوح' => 'bg-emerald-100 text-emerald-700',
            'منتهي' => 'bg-gray-100 text-gray-700',
        ][$this->status];
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // داخل App\Models\Meeting.php

    public function participants()
    {
        // الربط مع جدول المستخدمين عبر جدول meeting_participants مع جلب عمود الحالة status
        return $this->belongsToMany(User::class, 'meeting_participants', 'project_meeting_id', 'user_id')
            ->withPivot('status')
            ->withTimestamps();
    }
}
