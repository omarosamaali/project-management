<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'system_id',
        'payment_id',
        'course_id',
        'special_request_id',
        'request_payment_id',
        'amount',
        'original_price',
        'fees',
        'status',
        'store_id',
        'payment_method',
        'currency',
        'is_attended',
    ];

    protected $casts = [
        'is_attended' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // داخل ملف Payment.php
    public function requestPayment()
    {
        return $this->belongsTo(RequestPayment::class, 'request_payment_id');
    }
    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function store()
    {
        return $this->belongsTo(MyStore::class);
    }
    // App\Models\Payment.php

    public function specialRequest()
    {
        return $this->belongsTo(SpecialRequest::class, 'special_request_id');
    }
}
