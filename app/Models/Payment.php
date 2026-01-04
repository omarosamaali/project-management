<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'system_id',
        'payment_id',
        'amount',
        'original_price',
        'fees',
        'status',
        'payment_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }
}
