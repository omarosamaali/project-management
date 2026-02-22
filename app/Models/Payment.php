<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'system_id',
        'payment_id',
        'course_id', // تأكد من وجود هذا السطر
        'amount',
        'original_price',
        'fees',
        'status',
        'store_id',
        'payment_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // داخل ملف Payment.php
    public function requestPayment()
    {
        return $this->belongsTo(RequestPayment::class, 'payment_id');
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
