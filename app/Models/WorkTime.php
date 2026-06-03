<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkTime extends Model
{
    public const SOURCE_WEB = 'web';
    public const SOURCE_MANUAL = 'manual';

    protected $fillable = [
        'user_id',
        'country',
        'type',
        'source',
        'date',
        'start_time',
        'end_time',
        'notes',
        'work_days',
        'timezone',
    ];

    public function isFromWeb(): bool
    {
        if (($this->source ?? self::SOURCE_MANUAL) === self::SOURCE_WEB) {
            return true;
        }

        return str_contains((string) ($this->notes ?? ''), 'أزرار الدوام');
    }

    public function sourceLabel(): string
    {
        return $this->isFromWeb() ? 'الموقع (ويب)' : 'يدوي';
    }

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCountryNameAttribute()
    {
        return \App\Support\CountryNames::forCode($this->country) ?? strtoupper((string) $this->country);
    }
}