<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'special_request_id',
        'user_id',
        'title',
        'description',
        'image',
        'assigned_to',
        'status',
        'assigned_users'
    ];
    protected $casts = [
        'assigned_users' => 'array',
    ];
    /**
     * العلاقة مع طلب المشروع الأساسي
     */
    public function specialRequest()
    {
        return $this->belongsTo(SpecialRequest::class);
    }

    /**
     * العلاقة مع المستخدم الذي قام بإضافة المشكلة
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * دوال مساعدة للتحقق من الحالة (اختياري - تسهل عليك العمل في الـ Blade)
     */
    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function isDiscussing()
    {
        return $this->status === 'discussing';
    }

    // app/Models/Issue.php

// داخل ملف app\Models\Issue.php

    /**
     * العلاقة مع التعليقات (IssueComment)
     */
    public function comments()
    {
        // تأكد من استخدام اسم الكلاس الصحيح "IssueComment"
        return $this->hasMany(IssueComment::class, 'issue_id');
    }

    // app/Models/Issue.php

    public function solutionComment()
    {
        // نربط الحقل solution_comment_id بالموديل IssueComment
        return $this->belongsTo(IssueComment::class, 'solution_comment_id');
    }
}
