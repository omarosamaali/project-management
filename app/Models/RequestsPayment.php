<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestsPayment extends Model
{
    // أخبر لارافيل باسم الجدول الصحيح لأنه بالجمع
    protected $table = 'requests_payments';

    protected $fillable = [
        'request_id',
        'payment_name', // تأكد أنها payment_name وليست name
        'amount',
        'due_date',
        'status'
    ];

    public function request()
    {
        return $this->belongsTo(Requests::class, 'request_id');
    }
}