<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','facebook_id','google_id','online','birth_date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function books()
    {
        return $this->hasMany('App\Book');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Role','role_user',"user_id",'role_id');
    }
    public function isOnline()
    {
        return Cache::has('user-is-online-' . $this->id);
    }
}
