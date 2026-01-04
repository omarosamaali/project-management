<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPayment extends Model
{
    protected $fillable = [
        'special_request_id',
        'payment_name',
        'amount',
        'due_date',
        'status',
        'paid_at',
        'payment_notes',
        'payment_proof'
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'date',
    ];

    // علاقة مع المشروع
    public function specialRequest()
    {
        return $this->belongsTo(SpecialRequest::class);
    }

    // حالة الدفع بالعربي
    public function getStatusLabelAttribute()
    {
        return [
            'unpaid' => 'غير مدفوعة',
            'paid' => 'مدفوعة',
            'pending' => 'قيد المراجعة'
        ][$this->status] ?? 'غير محدد';
    }

    // لون حالة الدفع
    public function getStatusColorAttribute()
    {
        return [
            'unpaid' => 'red',
            'paid' => 'green',
            'pending' => 'yellow'
        ][$this->status] ?? 'gray';
    }
}
