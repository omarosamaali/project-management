<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestNote extends Model
{
protected $fillable = ['request_id', 'user_id', 'title', 'description', 'visible_to_client'];
protected $casts = ['visible_to_client' => 'boolean'];

public function user() {
return $this->belongsTo(User::class);
}
}