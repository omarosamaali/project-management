<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerCashoutRequest extends Model
{
    public const STATUS_PENDING_ADMIN = 'pending_admin';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PENDING_TRAINER_CONFIRM = 'pending_trainer_confirm';
    public const STATUS_PAID = 'paid';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'status',
        'payout_method_id',
        'payout_snapshot',
        'available_balance_snapshot',
        'trainer_confirm_due_at',
        'trainer_confirmed_at',
        'admin_note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'available_balance_snapshot' => 'decimal:2',
        'payout_snapshot' => 'array',
        'trainer_confirm_due_at' => 'datetime',
        'trainer_confirmed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payoutMethod()
    {
        return $this->belongsTo(PayoutMethod::class, 'payout_method_id');
    }

    public function screenshots()
    {
        return $this->hasMany(TrainerCashoutScreenshot::class, 'trainer_cashout_request_id')->latest('id');
    }

    public function hasSuccessScreenshot(): bool
    {
        if ($this->relationLoaded('screenshots')) {
            return $this->screenshots->contains(
                fn (TrainerCashoutScreenshot $shot) => $shot->kind === TrainerCashoutScreenshot::KIND_SUCCESS
            );
        }

        return $this->screenshots()
            ->where('kind', TrainerCashoutScreenshot::KIND_SUCCESS)
            ->exists();
    }

    public function canUploadScreenshots(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING_ADMIN,
            self::STATUS_PROCESSING,
            self::STATUS_PENDING_TRAINER_CONFIRM,
        ], true);
    }

    public function canMarkReadyForTrainer(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING_ADMIN, self::STATUS_PROCESSING], true)
            && $this->hasSuccessScreenshot();
    }

    public function canTrainerConfirm(): bool
    {
        return $this->status === self::STATUS_PENDING_TRAINER_CONFIRM
            && $this->trainer_confirmed_at === null
            && $this->hasSuccessScreenshot();
    }

    public function canReject(): bool
    {
        return ! in_array($this->status, [self::STATUS_PAID, self::STATUS_REJECTED], true);
    }

    public function isPending(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING_ADMIN,
            self::STATUS_PROCESSING,
            self::STATUS_PENDING_TRAINER_CONFIRM,
        ], true);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function statusLabel(): string
    {
        $key = 'messages.trainer_cashout_status_'.$this->status;

        return __($key) !== $key ? __($key) : $this->status;
    }
}
