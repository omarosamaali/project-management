<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestPrice extends Model
{
    protected $table = 'request_price';

    protected $fillable = [
        'request_id',
        'payment_name',
        'amount',
        'due_date',
        'status',
        'paid_at',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(Requests::class, 'request_id');
    }
}