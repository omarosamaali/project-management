<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PrivateCourseRequest extends Model
{
    public const STATUS_PENDING_TRAINER = 'pending_trainer';
    public const STATUS_DATES_PROPOSED = 'dates_proposed';
    public const STATUS_DATES_CHANGE_REQUESTED = 'dates_change_requested';
    public const STATUS_AWAITING_PAYMENT = 'awaiting_payment';
    public const STATUS_PAID = 'paid';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_EXPIRED_UNPAID = 'expired_unpaid';
    public const STATUS_EXPIRED_BUSY = 'expired_busy';
    public const STATUS_CANCELED_NO_MEETING = 'canceled_no_meeting';
    public const STATUS_BLOCKED = 'blocked';

    protected $fillable = [
        'source_course_id',
        'trainee_id',
        'trainer_id',
        'private_price',
        'status',
        'proposed_start_at',
        'proposed_end_at',
        'dates_accepted_by_trainer_at',
        'dates_accepted_by_trainee_at',
        'rejection_reason',
        'blocked_at',
        'blocked_by',
        'block_reason',
        'payment_id',
        'payment_due_at',
        'private_course_id',
        'trainer_responded_at',
        'expires_at',
        'trainee_note',
    ];

    protected $casts = [
        'private_price' => 'decimal:2',
        'proposed_start_at' => 'datetime',
        'proposed_end_at' => 'datetime',
        'dates_accepted_by_trainer_at' => 'datetime',
        'dates_accepted_by_trainee_at' => 'datetime',
        'blocked_at' => 'datetime',
        'payment_due_at' => 'datetime',
        'trainer_responded_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function sourceCourse()
    {
        return $this->belongsTo(Course::class, 'source_course_id');
    }

    public function privateCourse()
    {
        return $this->belongsTo(Course::class, 'private_course_id');
    }

    public function trainee()
    {
        return $this->belongsTo(User::class, 'trainee_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function events()
    {
        return $this->hasMany(PrivateCourseRequestEvent::class)->latest();
    }

    public function refund()
    {
        return $this->hasOne(PrivateCourseRefund::class);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING_TRAINER,
            self::STATUS_DATES_PROPOSED,
            self::STATUS_DATES_CHANGE_REQUESTED,
            self::STATUS_AWAITING_PAYMENT,
        ], true);
    }

    public function datesFullyAccepted(): bool
    {
        return $this->dates_accepted_by_trainer_at && $this->dates_accepted_by_trainee_at;
    }

    public function statusLabel(): string
    {
        return __('messages.private_request_status_'.$this->status);
    }
}
