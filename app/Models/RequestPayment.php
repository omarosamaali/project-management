<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestPayment extends Model
{
    protected $fillable = [
        'special_request_id',
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

    public function specialRequest()
    {
        return $this->belongsTo(SpecialRequest::class);
    }
}
