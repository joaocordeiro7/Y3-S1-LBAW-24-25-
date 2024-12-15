<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicProposal extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'topic_proposal';

    protected $primaryKey = 'proposal_id';
    
}
