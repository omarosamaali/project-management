<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyService extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'user_id',
        'price',
        'execution_days_from',
        'execution_days_to',
        'description_ar',
        'description_en',
        'requirements',
        'system_type',
        'features',
        'main_image',
        'images',
        'status',
        'support_days',
        'service_id',
        'original_price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'execution_days_from' => 'integer',
        'execution_days_to' => 'integer',
        'requirements' => 'array',
        'features' => 'array',
        'images' => 'array',
    ];

    public function service(){
        return $this->belongsTo(Service::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function requests()
    {
        return $this->hasMany(Requests::class);
    }

    public function partners()
    {
        return $this->belongsToMany(User::class, 'partner_system', 'system_id', 'partner_id')
            ->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'system_id');
    }
}
