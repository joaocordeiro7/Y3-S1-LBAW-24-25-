<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public $timestamps  = false;

    protected $table = 'posts';

    protected $primaryKey = 'post_id';

    protected $fillable = ['title','body','updated_at','upvotes','downvotes','ownerid'];

    protected $casts = ['created_at'=> 'datetime','updated_at'=> 'datetime'];


    public function owner() {
        return $this->belongsTo(User::class,'ownerid');
    }

    public function ownerName() {
      $user =User::findOrFail($this->owner())->name;
      return $user;
    }

    public function comments(){
      return $this->hasMany(Comment::class, 'post', 'post_id')->orderBy('created_at', 'desc');
    }

    public function tags(){
      return $this->belongsToMany(Tag::class, 'post_tags', 'post', 'tag');
    }


}