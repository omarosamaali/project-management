<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewProject extends Model
{
    use HasFactory;

    // 1. تحديد اسم الجدول الفعلي في قاعدة البيانات
    protected $table = 'special_requests';

    // 2. الحقول المسموح بتعبئتها (تأكد من مطابقتها لأعمدة الجدول عندك)
    protected $fillable = [
        'title',
        'description',
        'project_type',
        'user_id',
        'status', // أو أي حقول أخرى موجودة في جدول special_requests
    ];

    // 3. علاقة المشروع بصاحب المشروع (المستخدم)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 4. علاقة المشروع بالعروض المقدمة عليه (Proposals)
    public function proposals()
    {
        // سننشئ موديل Proposal لاحقاً لربط العروض بالمشروع
        return $this->hasMany(ProjectProposal::class, 'project_id');
    }

    // 5. "Accessor" لتحويل الحالة إلى نص مقروء (اختياري حسب كودك)
    public function getStatusNameAttribute()
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'in_review' => 'قيد المراجعة',
            'in_progress' => 'قيد المعالجة',
            'completed' => 'مكتمل',
            'canceled' => 'ملغية',
            'بانتظار الدفع' => 'بانتظار الدفع',

        ];
        return $statuses[$this->status] ?? 'غير محدد';
    }
}
