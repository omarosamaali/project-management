<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TrainerCashoutScreenshot extends Model
{
    public const KIND_PENDING = 'pending';
    public const KIND_SUCCESS = 'success';
    public const KIND_FAIL = 'fail';

    protected $fillable = [
        'trainer_cashout_request_id',
        'kind',
        'path',
        'uploaded_by',
    ];

    public function request()
    {
        return $this->belongsTo(TrainerCashoutRequest::class, 'trainer_cashout_request_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return route('dashboard.academy.cashouts.screenshot-file', $this);
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
