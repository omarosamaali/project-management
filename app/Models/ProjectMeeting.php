<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMeeting extends Model
{
    protected $table = 'project_meetings';
    protected $fillable = ['special_request_id', 'request_id', 'created_by', 'title', 'meeting_link', 'meeting_type', 'start_at', 'end_at'];
    protected $casts = ['start_at' => 'datetime', 'end_at' => 'datetime'];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'meeting_participants', 'project_meeting_id', 'user_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute()
    {
        $now = now();
        if ($now->lt($this->start_at)) return 'قادم';
        if ($now->between($this->start_at, $this->end_at)) return 'جارٍ الآن';
        return 'منتهي';
    }

    public function getStatusColorAttribute()
    {
        return [
            'قادم' => 'bg-blue-100 text-blue-700',
            'جارٍ الآن' => 'bg-green-100 text-green-700',
            'منتهي' => 'bg-gray-100 text-gray-700',
        ][$this->status_label];
    }

    public function projectRequest()
    {
        return $this->belongsTo(Requests::class, 'request_id');
    }

    public function getMeetingTypeLabelAttribute(): string
    {
        return $this->meeting_type === 'in_person' ? 'حضوري' : 'أونلاين';
    }

    public function getMeetingTypeBadgeAttribute(): string
    {
        return $this->meeting_type === 'in_person'
            ? 'bg-orange-100 text-orange-700'
            : 'bg-blue-100 text-blue-700';
    }

    public function formattedDateRange(string $timezone = 'Asia/Dubai'): string
    {
        $arabicDays = [
            'Sunday' => 'الأحد', 'Monday' => 'الاثنين', 'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة', 'Saturday' => 'السبت',
        ];
        $arabicMonths = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو',
            7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ];

        $start = $this->start_at->timezone($timezone);
        $end   = $this->end_at->timezone($timezone);

        $dayName   = $arabicDays[$start->format('l')];
        $monthName = $arabicMonths[(int) $start->format('n')];
        $dayNum    = $start->format('j');
        $amPm      = $end->format('A') === 'AM' ? 'ص' : 'م';

        return "{$dayName}، {$dayNum} {$monthName} · {$start->format('g:i')} – {$end->format('g:i')} {$amPm}";
    }
}
