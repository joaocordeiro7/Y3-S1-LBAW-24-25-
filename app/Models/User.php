<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Added to define Eloquent relationships.
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    
    

    public function posts(): HasMany{
        return $this->hasMany(Post::class,'ownerid');
    }

    public function isAdmin(): bool {
        return $this->hasOne(Admin::class, 'admin_id', 'user_id')->exists();
    }

    // the convetion is userId1 follows userId2 
    public function follows(): HasMany{
        return $this->hasMany(User::class,"followed_users",'userid1');
    }

    
    public function followedBy(): HasMany{
        return $this->hasMany(User::class,"followed_users",'userid2');
    }

    public static function alreadyFollows($user2): bool{
        return DB::table('follwed_users')->where('userid1',"=",Auth::id())->where('userid2',"=",$user2)->exists();
    }

}
