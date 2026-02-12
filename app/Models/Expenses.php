<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    protected $fillable = [
        'special_request_id',
        'user_id',
        'title',
        'description',
        'price',
        'date',
        'image',
    ];

    public function specialRequest()
    {
        return $this->belongsTo(SpecialRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
