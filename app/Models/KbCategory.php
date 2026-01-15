<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KbCategory extends Model
{
    protected $fillable = ['icon', 'title', 'status', 'created_by', 'updated_by'];

    public function entries()
    {
        return $this->hasMany(KnowledgeBase::class, 'category_id');
    }

    public function knowledges()
    {
        return $this->hasMany(KnowledgeBase::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
