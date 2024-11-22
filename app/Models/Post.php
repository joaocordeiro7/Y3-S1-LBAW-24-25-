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




    public function owner() {

        return $this->belongsTo(User::class,'ownerid');
      
    }

    public function ownerName() {
      $user =User::findOrFail($this->owner())->name;
      return $user;
    }
    
}
