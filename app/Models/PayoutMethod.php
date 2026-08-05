<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PayoutMethod extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'image_path',
        'is_active',
        'is_system',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function fields()
    {
        return $this->hasMany(PayoutMethodField::class)->orderBy('sort_order')->orderBy('id');
    }

    public function paymentProfiles()
    {
        return $this->hasMany(TrainerPaymentProfile::class);
    }

    public function cashoutRequests()
    {
        return $this->hasMany(TrainerCashoutRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function title(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        if ($locale === 'en') {
            return $this->name_en ?: $this->name_ar;
        }

        return $this->name_ar ?: $this->name_en;
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }

    public function isBankTransfer(): bool
    {
        return (bool) $this->is_system;
    }
}
