<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueComment extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'issue_id',
        'user_id',
        'comment',
        'image',
        'is_solution'
    ];

    /**
     * تحويل الأنواع
     */
    protected $casts = [
        'is_solution' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقة مع المشكلة (Issue)
     */
    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    /**
     * العلاقة مع المستخدم الذي كتب التعليق
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * دالة مساعدة للتحقق من أن التعليق هو الحل
     */
    public function isSolution()
    {
        return $this->is_solution === true;
    }

    /**
     * دالة مساعدة للتحقق من أن المستخدم الحالي هو صاحب التعليق
     */
    public function isOwnedBy($userId)
    {
        return $this->user_id === $userId;
    }
}
