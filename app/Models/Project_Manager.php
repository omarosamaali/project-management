<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project_Manager extends Model
{
    protected $table = 'project__managers';

    protected $fillable = [
        'special_request_id',
        'request_id', // أضف هذا الحقل
        'user_id',
    ];

    public function request()
    {
        // علاقة مع موديل Requests (بصيغة الجمع كما سميته أنت)
        return $this->belongsTo(Requests::class, 'request_id');
    }
    public function specialRequest()
    {
        // مدير المشروع ينتمي لطلب خاص واحد
        return $this->belongsTo(SpecialRequest::class, 'special_request_id');
    }

    public function user()
    {
        // مدير المشروع هو مستخدم (User)
        return $this->belongsTo(User::class, 'user_id');
    }
}
