<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectNote extends Model
{
    protected $fillable = [
        'special_request_id',
        'user_id',
        'title',
        'description',
        'visible_to_client'
    ];

    protected $casts = [
        'visible_to_client' => 'boolean',
    ];

    // علاقة مع المستخدم (صاحب الملاحظة)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة مع المشروع
    public function specialRequest()
    {
        return $this->belongsTo(SpecialRequest::class);
    }
}
