<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PrivateCourseRefundScreenshot extends Model
{
    public const KIND_PENDING = 'pending';
    public const KIND_SUCCESS = 'success';
    public const KIND_FAIL = 'fail';

    protected $fillable = [
        'private_course_refund_id',
        'path',
        'kind',
        'note',
        'uploaded_by',
    ];

    public function refund()
    {
        return $this->belongsTo(PrivateCourseRefund::class, 'private_course_refund_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return route('dashboard.academy.private-refunds.screenshot-file', $this);
    }

    public function kindLabel(): string
    {
        $key = 'messages.private_refund_shot_kind_'.$this->kind;

        return __($key) !== $key ? __($key) : $this->kind;
    }

    public function existsOnDisk(): bool
    {
        return filled($this->path) && Storage::disk('public')->exists($this->path);
    }
}
