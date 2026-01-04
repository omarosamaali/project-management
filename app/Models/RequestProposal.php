<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestProposal extends Model
{
    protected $table = 'request_proposals';
    protected $fillable = ['request_id', 'user_id', 'budget_to', 'execution_time', 'proposal_details', 'status'];

    public function request()
    {
        return $this->belongsTo(Requests::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}