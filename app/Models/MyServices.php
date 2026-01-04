<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyServices extends Model
{
    protected $table = 'my_service';

    protected $fillable = [
        'user_id',
        'selected_services',
    ];

    protected $casts = [
        'selected_services' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
