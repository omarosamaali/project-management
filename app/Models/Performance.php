<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Performance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'response_speed',
        'execution_time',
        'message_response_rate',
        'support_tickets_closed',
        'completed_tasks',
        'performance_date',
    ];

    protected $casts = [
        'performance_date' => 'date',
        'execution_time' => 'decimal:2',
    ];

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // حساب النسبة المئوية لسرعة الرد (كلما أقل كلما أفضل)
    public function getResponseSpeedScoreAttribute()
    {
        // الهدف: 20 دقيقة أو أقل = 100%
        $target = 20;
        if ($this->response_speed <= $target) {
            return 100;
        }
        // كل دقيقة زيادة تقلل 5%
        $score = 100 - (($this->response_speed - $target) * 5);
        return max(0, $score);
    }

    // حساب النسبة المئوية لمدة التنفيذ
    public function getExecutionTimeScoreAttribute()
    {
        // الهدف: 5 أيام أو أقل = 100%
        $target = 5;
        if ($this->execution_time <= $target) {
            return 100;
        }
        $score = 100 - (($this->execution_time - $target) * 10);
        return max(0, $score);
    }

    // النتيجة الإجمالية
    public function getTotalScoreAttribute()
    {
        return round((
            $this->response_speed_score +
            $this->execution_time_score +
            $this->message_response_rate +
            min(100, ($this->support_tickets_closed / 100) * 100) +
            min(100, ($this->completed_tasks / 40) * 100)
        ) / 5);
    }
}