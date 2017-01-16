<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    public $timestamps = true;
    protected $fillable = ['sender_id','recipient_id','message','seen','deleted','deleted_by','image'];
}
