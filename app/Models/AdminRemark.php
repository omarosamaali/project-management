<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRemark extends Model
{
    protected $fillable = ['user_id', 'details', 'image'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
