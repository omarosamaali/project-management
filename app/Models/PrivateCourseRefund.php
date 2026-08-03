<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateCourseRefund extends Model
{
    public const STATUS_REQUIRED = 'required';
    public const STATUS_PENDING_TRAINEE_CONFIRM = 'pending_trainee_confirm';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'private_course_request_id',
        'payment_id',
        'trainee_id',
        'amount',
        'currency',
        'status',
        'admin_id',
        'screenshot_path',
        'screenshot_uploaded_at',
        'admin_note',
        'trainee_confirmed_at',
        'trainee_confirm_due_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'screenshot_uploaded_at' => 'datetime',
        'trainee_confirmed_at' => 'datetime',
        'trainee_confirm_due_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(PrivateCourseRequest::class, 'private_course_request_id');
    }

    public function trainee()
    {
        return $this->belongsTo(User::class, 'trainee_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function screenshots()
    {
        return $this->hasMany(PrivateCourseRefundScreenshot::class)->latest('id');
    }

    public function screenshotCount(): int
    {
        if ($this->relationLoaded('screenshots')) {
            return $this->screenshots->count();
        }

        return (int) $this->screenshots()->count();
    }

    public function hasSuccessScreenshot(): bool
    {
        if ($this->relationLoaded('screenshots')) {
            return $this->screenshots->contains(
                fn (PrivateCourseRefundScreenshot $shot) => $shot->kind === PrivateCourseRefundScreenshot::KIND_SUCCESS
            );
        }

        return $this->screenshots()
            ->where('kind', PrivateCourseRefundScreenshot::KIND_SUCCESS)
            ->exists();
    }

    public function canMarkReadyForTrainee(): bool
    {
        return $this->status === self::STATUS_REQUIRED
            && $this->hasSuccessScreenshot();
    }

    public function canTraineeConfirm(): bool
    {
        return $this->status === self::STATUS_PENDING_TRAINEE_CONFIRM
            && $this->trainee_confirmed_at === null
            && $this->hasSuccessScreenshot();
    }

    public function canUploadScreenshots(): bool
    {
        return in_array($this->status, [
            self::STATUS_REQUIRED,
            self::STATUS_PENDING_TRAINEE_CONFIRM,
        ], true);
    }

    /**
     * Primary proof URL (legacy column or latest screenshot).
     */
    public function screenshotUrl(): ?string
    {
        if ($this->relationLoaded('screenshots') && $this->screenshots->isNotEmpty()) {
            return $this->screenshots->first()->url();
        }

        $latest = $this->screenshots()->first();
        if ($latest) {
            return $latest->url();
        }

        if (! $this->screenshot_path) {
            return null;
        }

        return route('dashboard.academy.private-refunds.screenshot', $this);
    }
}
