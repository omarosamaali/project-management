<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AvailableService extends Model
{
    protected $table = 'available_services';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // إنشاء slug تلقائياً
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
    }

    // Scope للخدمات النشطة
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope للترتيب
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
