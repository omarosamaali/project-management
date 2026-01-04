<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnicalSupport extends Model
{
    protected $table = 'technical_support';

    protected $fillable = [
        'request_id',
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

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class, 'system_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

}