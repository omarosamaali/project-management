<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialRequestPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'special_request_id',
        'payment_name',
        'amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // العلاقة مع الطلب الخاص
    public function specialRequest()
    {
        return $this->belongsTo(SpecialRequest::class);
    }

    // Helper methods
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}
