<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerSystem extends Model
{
    protected $table = 'partner_system';
    protected $fillable = ['partner_id', 'system_id'];

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function system()
    {
        return $this->belongsTo(System::class, 'system_id');
    }
}