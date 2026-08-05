<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TrainerPaymentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'payout_method_id',
        'field_values',
        'bank_account_name',
        'bank_iban',
        'bank_name',
        'bank_country',
        'id_card_front_path',
        'id_card_back_path',
        'configured_at',
        'locked_at',
    ];

    protected $casts = [
        'field_values' => 'array',
        'configured_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function method()
    {
        return $this->belongsTo(PayoutMethod::class, 'payout_method_id');
    }

    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    public function isComplete(): bool
    {
        if (! $this->payout_method_id || ! $this->id_card_front_path || ! $this->id_card_back_path) {
            return false;
        }

        $method = $this->method;
        if (! $method) {
            return false;
        }

        if ($method->isBankTransfer()) {
            return filled($this->bank_account_name)
                && filled($this->bank_iban)
                && filled($this->bank_name)
                && filled($this->bank_country);
        }

        $values = $this->field_values ?? [];
        foreach ($method->fields as $field) {
            if ($field->is_required && ! filled($values[$field->key] ?? null)) {
                return false;
            }
        }

        return true;
    }

    public function idCardFrontUrl(): ?string
    {
        return $this->id_card_front_path ? Storage::url($this->id_card_front_path) : null;
    }

    public function idCardBackUrl(): ?string
    {
        return $this->id_card_back_path ? Storage::url($this->id_card_back_path) : null;
    }
}
