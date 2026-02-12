<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialRequestMessage extends Model
{
protected $fillable = ['special_request_id', 'user_id', 'message'];

// علاقة الرسالة بالمستخدم (عشان نجيب اسمه وصورته)
public function user(): BelongsTo
{
return $this->belongsTo(User::class);
}

// علاقة الرسالة بالطلب
public function specialRequest(): BelongsTo
{
return $this->belongsTo(SpecialRequest::class, 'special_request_id');
}
}