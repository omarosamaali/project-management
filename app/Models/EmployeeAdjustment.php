<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAdjustment extends Model
{
    protected $fillable = ['user_id', 'type', 'amount', 'date', 'notes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'date' => 'date', // أو 'datetime' إذا كان يحتوي على وقت
    ];
}