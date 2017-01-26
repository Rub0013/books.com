<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    public $timestamps = true;
    protected $fillable = ['name','author','genre','user_id','image','price'];

    public function booklike()
    {
        return $this->hasOne('App\Booklike');
    }
    public function booklikes()
    {
        return $this->hasMany('App\Booklike');
    }
}
