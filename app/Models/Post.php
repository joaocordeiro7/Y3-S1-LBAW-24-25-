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


    protected $fillable = ['title','body','updated_at','upvotes','downvotes'];




    public function owner() {

        return $this->belongsTo(User::class,'ownerid');
      
      }
    
}
