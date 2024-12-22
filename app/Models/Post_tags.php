<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'post_tags';

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'post', 'tag',
    ];

}