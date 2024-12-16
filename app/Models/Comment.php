<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    
    public $timestamps  = false;

    protected $table = 'comments';

    protected $primaryKey = 'comment_id';


    protected $fillable = ['body','updated_at','upvotes','downvotes','post', 'reply_to', 'ownerid'];

    protected $casts = ['created_at'=> 'datetime','updated_at'=> 'datetime'];


    public function owner() {

        return $this->belongsTo(User::class,'ownerid');
      
    }

    public function ownerName() {
      $user =User::findOrFail($this->owner())->name;
      return $user;
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post', 'post_id');
    }
}
