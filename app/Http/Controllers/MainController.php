<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Chat;
use Auth;
use DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class MainController extends Controller
{
    public function total_notes_count()
    {
        if(Auth::check())
        {
            $notes = DB::table('users')->select(DB::raw('count(users.id), users.name'))->groupBy('users.id')
                ->join('chats', function($join)
                {
                    $join->on('users.id', '=', 'sender_id')
                        ->Where('recipient_id','=',Auth::user()->id)
                        ->where('seen','=',0);
                })
                ->get();
            $messages = Count($notes);
        }
        else
        {
            $messages = 0;
        }
        return $messages;
    }
    public function total_requests()
    {
        if(Auth::check())
        {
            $new_requests = DB::table('users')->select('users.id as person_id','name','answer','friends.id as request_id')
                ->where('request_to_id','=',Auth::user()->id)
                ->join('friends', function($join)
                {
                    $join->on('users.id', '=', 'request_from_id')
                        ->where('answer','=',0);
                })
                ->get();
            if(count($new_requests)>0)
            {
                $answer = $new_requests;
            }
            else
            {
                $answer = array();
            }
        }
        else
        {
            $answer = array();
        }
        return $answer;
    }
}
