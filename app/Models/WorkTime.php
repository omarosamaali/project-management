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

    // app/Models/WorkTime.php

    public function getCountryNameAttribute()
    {
        // تحويل الكود لحروف كبيرة (مثلاً من ao إلى AO)
        $countryCode = strtoupper($this->country);

        if (class_exists('Locale')) {
            // نستخدم الكود الكبير مع Locale
            $name = \Locale::getDisplayRegion('-' . $countryCode, 'ar');
            return $name ?: $countryCode;
        }

        // حل بديل في حال لم تعمل المكتبة (أشهر الدول التي قد تستخدمها)
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