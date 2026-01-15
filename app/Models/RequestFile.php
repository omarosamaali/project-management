<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestFile extends Model
{
    protected $table = 'request_files'; // تأكيد اسم الجدول
    protected $fillable = ['request_id', 'user_id', 'title', 'description', 'file_path', 'file_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function request()
    {
        return $this->belongsTo(Requests::class);
    }
}