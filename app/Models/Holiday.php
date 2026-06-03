<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Holiday extends Model
{
    public const TYPE_GENERAL = 'general';

    public const TYPE_PRIVATE = 'private';

    public const SALARY_PAID = 'paid';

    public const SALARY_UNPAID = 'unpaid';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'type',
        'name',
        'start_date',
        'end_date',
        'salary_deduction_status',
        'details',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'holiday_user')->withTimestamps();
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function typeLabel(): string
    {
        return $this->type === self::TYPE_GENERAL ? 'عامة' : 'خاصة';
    }

    public function salaryStatusLabel(): string
    {
        return $this->salary_deduction_status === self::SALARY_PAID ? 'مدفوعة' : 'غير مدفوعة';
    }

    public function statusLabel(): string
    {
        return $this->status === self::STATUS_ACTIVE ? 'فعالة' : 'غير فعالة';
    }
}
