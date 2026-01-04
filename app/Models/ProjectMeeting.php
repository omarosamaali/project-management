<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMeeting extends Model
{
    protected $table = 'project_meetings';
    protected $fillable = ['special_request_id', 'created_by', 'title', 'meeting_link', 'start_at', 'end_at'];
    protected $casts = ['start_at' => 'datetime', 'end_at' => 'datetime'];

    // علاقة المشاركين (الجدول الوسيط)
    public function participants()
    {
        return $this->belongsToMany(User::class, 'meeting_participants', 'project_meeting_id', 'user_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    // من أنشأ الاجتماع
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors للحالة واللون
    public function getStatusLabelAttribute()
    {
        $now = now();
        if ($now->lt($this->start_at)) return 'قادم';
        if ($now->between($this->start_at, $this->end_at)) return 'جارٍ الآن';
        return 'منتهٍ';
    }

    public function getStatusColorAttribute()
    {
        return [
            'قادم' => 'bg-blue-100 text-blue-700',
            'جارٍ الآن' => 'bg-green-100 text-green-700',
            'منتهٍ' => 'bg-gray-100 text-gray-700',
        ][$this->status_label];
    }

    
}
