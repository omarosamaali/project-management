<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkTime extends Model
{
    protected $fillable = [
        'user_id',
        'country',
        'type',
        'date',
        'start_time',
        'end_time',
        'notes',
        'work_days',
        'timezone'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCountryNameAttribute()
    {
        $countryCode = strtoupper($this->country);

        if (class_exists('Locale')) {
            $name = \Locale::getDisplayRegion('-' . $countryCode, 'ar');
            return $name ?: $countryCode;
        }

        $manualCountries = [
            'EG' => 'مصر',
            'SA' => 'السعودية',
            'AE' => 'الإمارات',
            'JO' => 'الأردن',
            'AO' => 'أنجولا',
        ];

        return $manualCountries[$countryCode] ?? $countryCode;
    }
}