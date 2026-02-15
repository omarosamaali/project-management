<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MyStore extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'price',
        'execution_days',
        'description_ar',
        'description_en',
        'requirements',
        'system_type',
        'features',
        'buttons',
        'original_price',
        'main_image',
        'images',
        'status',
        'support_days',
        'service_id',
        'system_external',
        'user_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'execution_days' => 'integer',
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

    public function payments()
    {
        return $this->hasMany(Payment::class, 'store_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function isUserEnrolled()
    {
        if (!Auth::check()) return false;

        return $this->payments()
            ->where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'success', 'paid', 'active', 'pending'])
            ->exists();
    }
}
