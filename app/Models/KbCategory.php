<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KbCategory extends Model
{
    protected $fillable = ['icon', 'title', 'status'];

    public function entries()
    {
        return $this->hasMany(KnowledgeBase::class, 'category_id');
    }
}
