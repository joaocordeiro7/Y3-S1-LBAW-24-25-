<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InteractionPosts extends Model
{
    use HasFactory;

    public $timestamps  = false;

    protected $table = 'interationposts';

    protected $primaryKey = 'id';

    protected $fillable = ['userid', 'postid', 'liked'];
}