<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InteractionComments extends Model
{
    use HasFactory;

    public $timestamps  = false;

    protected $table = 'interationcomments';

    protected $primaryKey = 'id';

    protected $fillable = ['userid', 'comment_id', 'liked'];
}