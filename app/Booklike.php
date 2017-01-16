<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booklike extends Model
{
    public $timestamps = true;
    protected $fillable = ['book_id','liked_user_id','like'];
}
