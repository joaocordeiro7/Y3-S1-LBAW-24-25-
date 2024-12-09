<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blocked extends Model
{
    use HasFactory;

    public $timestamps  = false;

    protected $table = 'blocked';

    protected $primaryKey = 'blocked_id';

    public function user()
    {
        return $this->belongsTo(User::class, 'blocked_id', 'user_id');
    }
}
