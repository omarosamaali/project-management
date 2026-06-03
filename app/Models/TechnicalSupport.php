<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnicalSupport extends Model
{
    protected $table = 'technical_support';

    protected $fillable = [
        'request_id',
        'special_request_id',
        'system_id',
        'client_id',
        'subject',
        'description',
        'status',
    ];

    public function getStatusLabelAttribute() {
        $statuses = [
            'open' => 'مفتوحة',
            'in_review' => 'قيد المراجعة',
            'resolved' => 'محلولة',
            'closed' => 'منتهية'
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(Requests::class);
    }

    public function specialRequest(): BelongsTo
    {
        return $this->belongsTo(SpecialRequest::class, 'special_request_id');
    }

    public function getProjectNameAttribute(): string
    {
        if ($this->special_request_id && $this->relationLoaded('specialRequest')) {
            return $this->specialRequest?->title ?? ('مشروع خاص #' . $this->special_request_id);
        }

        if ($this->special_request_id) {
            return SpecialRequest::find($this->special_request_id)?->title
                ?? ('مشروع خاص #' . $this->special_request_id);
        }

        return $this->request?->system?->name_ar
            ?? $this->system?->name_ar
            ?? ('مشروع #' . ($this->request_id ?? $this->id));
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class, 'system_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

}