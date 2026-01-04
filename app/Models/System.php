<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'price',
        'execution_days_from',
        'execution_days_to',
        'description_ar',
        'description_en',
        'requirements',
        'system_type',
        'features',
        'buttons',
        'main_image',
        'images',
        'status',
        'support_days',
        'service_id',
        'counter',
        'system_external',
        'external_url'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'execution_days_from' => 'integer',
        'execution_days_to' => 'integer',
        'requirements' => 'array',
        'features' => 'array',
        'buttons' => 'array',
        'images' => 'array',
    ];

    public function requests()
    {
        return $this->hasMany(Requests::class);
    }

    public function partners()
    {
        return $this->belongsToMany(User::class, 'partner_system', 'system_id', 'partner_id')
            ->withTimestamps();
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // في app/Models/System.php
    public function payments()
    {
        return $this->hasMany(Payment::class, 'system_id');
    }
}
