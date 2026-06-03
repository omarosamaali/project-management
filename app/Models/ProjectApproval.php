<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectApproval extends Model
{
    protected $fillable = [
        'special_request_id',
        'user_id',
        'title',
        'description',
        'file_path',
        'file_type',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialRequest()
    {
        return $this->belongsTo(SpecialRequest::class);
    }

    public function approvers()
    {
        return $this->belongsToMany(User::class, 'project_approval_approver', 'project_approval_id', 'user_id')
            ->withPivot('approved_at')
            ->withTimestamps();
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function refreshApprovalStatus(): void
    {
        $required = $this->approvers()->count();
        if ($required === 0) {
            return;
        }

        $approvedCount = $this->approvers()->wherePivotNotNull('approved_at')->count();
        if ($approvedCount >= $required) {
            $this->update(['status' => 'approved']);
        }
    }
}
