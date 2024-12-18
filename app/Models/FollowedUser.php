<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class FollowedUser extends Model
{
    use HasFactory;

    protected $table = 'follwed_users'; 

    public $timestamps = false;

    protected $primaryKey = 'id';
}