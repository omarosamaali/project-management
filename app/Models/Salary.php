<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'overtime_value',
        'deduction_value',
        'carried_forward',
        'total_due',
        'attachment'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}