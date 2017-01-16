<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    public $timestamps = true;
    protected $fillable = ['request_from_id','request_to_id','answer','answer_seen'];
}
