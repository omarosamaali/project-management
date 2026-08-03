<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function screenshotUrl(): ?string
    {
        return $this->screenshot_path
            ? Storage::disk('public')->url($this->screenshot_path)
            : null;
    }
}
