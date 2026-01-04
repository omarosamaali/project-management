<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectProposal extends Model
{
    use HasFactory;

    // 1. تحديد اسم الجدول الذي أنشأناه في الـ Migration
    protected $table = 'project_proposals';

    // 2. الحقول المسموح بتعبئتها (Mass Assignment)
    protected $fillable = [
        'project_id',
        'user_id',
        'budget_to',
        'execution_time',
        'proposal_details',
        'status',
    ];

    // 3. علاقة العرض بالمشروع (كل عرض ينتمي لمشروع واحد من جدول special_requests)
    public function project()
    {
        // نربطه بموديل NewProject الذي يشير لجدول special_requests
        return $this->belongsTo(NewProject::class, 'project_id');
    }

    // 4. علاقة العرض بالمستخدم (من الذي قدم هذا العرض؟)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 5. دالة لتحسين عرض الحالة (اختياري)
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending'  => 'قيد الانتظار',
            'accepted' => 'تم القبول',
            'rejected' => 'مرفوض',
            default    => $this->status,
        };
    }
}
