<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'location_type',
        'online_link',
        'venue_name',
        'venue_map_url',
        'venue_details',
        'counter',
        'count_days',
        'external_url',
        'service_id',
        'price',
        'description_ar',
        'description_en',
        'requirements',
        'features',
        'buttons',
        'main_image',
        'images',
        'start_date',
        'end_date',
        'last_date',
        'status',
    ];

    protected $casts = [
        'system_external' => 'boolean',
        'price' => 'decimal:2',
        'requirements' => 'array',
        'features' => 'array',
        'buttons' => 'array',
        'images' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'last_date' => 'datetime',
        'counter' => 'integer',
        'count_days' => 'integer',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class, 'course_id');
    }
    public function isUserEnrolled()
    {
        if (!auth()->check()) return false;

        return $this->payments()
            ->where('user_id', auth()->id())
            ->whereIn('status', ['completed', 'success', 'paid', 'active', 'pending'])
            ->exists();
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user')->withPivot([
                'price_paid',
                'status',
                'enrolled_at',
                'expires_at'
            ])->withTimestamps();
    }
}
