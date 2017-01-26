<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Friend;
use App\User;
use App\Role;

class HomeController extends MainController
{
    public function __construct()
    {
        $this->middleware('check');
    }
    public function index()
    {

        $lang = new LanguageController();
        $logged = User::find(Auth::user()->id);
        $logged->online = 1;
        $logged->save();
        $friends = DB::table('users')->select('users.id','users.online','name','answer','friends.id as request_id')
            ->join('friends', function($join)
            {
                $join->on('users.id', '=', 'request_from_id')
                    ->Where('request_to_id','=',Auth::user()->id)
                    ->where('answer','=',1)
                    ->orOn('users.id', '=', 'request_to_id')
                    ->Where('request_from_id','=',Auth::user()->id)
                    ->where('answer','=',1);
            })
            ->get();
        $array = array('total_notes_count' => $this->total_notes_count(),
            'total_requests' => $this->total_requests(),
            'friends' => $friends);
        return view('home',$array);
    }
    public  function  total_notifications(Request $request)
    {
        $answer = array(
            'total_messages' => $this->total_notes_count(),
            'total_requests' => $this->total_requests());
        return $answer;
    }
}
