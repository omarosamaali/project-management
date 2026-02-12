<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialRequestPartner extends Model
{
    use HasFactory;

    protected $table = 'special_request_partner';

    protected $fillable = [
        'order_number',
        'special_request_id',
        'request_id',
        'partner_id',
        'notes',
        'status',
        'profit_share_percentage',
        'share_type',
        'fixed_amount',
    ];

    public function specialRequest(): BelongsTo
    {
        return $this->belongsTo(SpecialRequest::class);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(Requests::class);
    }
    
    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_id');
    }
    public function system()
    {
        return $this->hasOneThrough(
            System::class,
            SpecialRequest::class,
            'id',
            'id',
            'special_request_id',
            'system_id'
        );
    }

}
