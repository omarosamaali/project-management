<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestActivity extends Model
{
    protected $fillable = ['request_id', 'user_id', 'type', 'description', 'properties'];

    protected $casts = ['properties' => 'array'];

    public function request()
    {
        return $this->belongsTo(Requests::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}