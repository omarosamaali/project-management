<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectBudget extends Model
{
    protected $fillable = ['special_request_id', 'price', 'payment_type'];

    public function specialRequest()
    {
        return $this->belongsTo(SpecialRequest::class);
    }
}