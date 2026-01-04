<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyService extends Model
{
    protected $fillable = [
        'title',
        'service_id',
        'price',
        'duration',
        'description',
        'what_you_will_get',
        'user_id'
    ];

    public function service(){
        return $this->belongsTo(Service::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
