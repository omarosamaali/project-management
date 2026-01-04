<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionRequest extends Model
{
    protected $fillable = ['user_id', 'title', 'reason', 'details', 'session_time', 'session_link', 'invitees', 'status'];

    protected $casts = [
        'session_time' => 'datetime',
        'invitees' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusNameAttribute()
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'in_review' => 'قيد المراجعة',
            'in_progress' => 'قيد المعالجة',
            'completed' => 'مكتمل',
            'canceled' => 'ملغية',
            'بانتظار الدفع' => 'بانتظار الدفع',
            'confirmed' => 'تم التاكيد',

        ];
        return $statuses[$this->status] ?? 'غير محدد';
    }

    public function getParticipantStatus($email)
    {
        if (!$this->invitees) return null;

        foreach ($this->invitees as $invitee) {
            if ($invitee['email'] === $email) {
                return $invitee['status'] ?? 'pending';
            }
        }
        return null;
    }
}