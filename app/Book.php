<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    public $timestamps = true;
    protected $fillable = ['name','author','genre','user_id','image'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
