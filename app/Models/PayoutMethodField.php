<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutMethodField extends Model
{
    protected $fillable = [
        'payout_method_id',
        'key',
        'label_ar',
        'label_en',
        'input_type',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function method()
    {
        return $this->belongsTo(PayoutMethod::class, 'payout_method_id');
    }

    public function label(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        if ($locale === 'en') {
            return $this->label_en ?: $this->label_ar;
        }

        return $this->label_ar ?: $this->label_en;
    }
}
