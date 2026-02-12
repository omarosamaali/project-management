<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestMessage extends Model
{
    protected $table = 'request_messages';

    protected $fillable = [
        'request_id',
        'user_id',
        'message'
    ];

    public function request()
    {
        return $this->belongsTo(Requests::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
