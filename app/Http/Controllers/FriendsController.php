<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Friend;
use App\User;
use App\Chat;

class FriendsController extends MainController
{
    public function find_friend(Request $request)
    {
        $reg = preg_quote($request['friend_name']);
        $finded_friends = DB::table('users')->select('users.id', 'users.name', 'friends.answer', 'friends.request_from_id', 'friends.request_to_id', 'friends.id AS request_id')
            ->where('users.id','<>',Auth::user()->id)
            ->where('users.name','like','%'.$reg.'%')
            ->leftJoin('friends', function($join)
            {
                $join->on('users.id', '=', 'friends.request_from_id')
                    ->Where('request_to_id','=',Auth::user()->id)
                    ->orWhere('friends.request_to_id','=','Null')
                    ->orOn('users.id', '=', 'friends.request_to_id')
                    ->Where('friends.request_from_id','=',Auth::user()->id)
                    ->orWhere('friends.request_from_id','=','Null');
            })
            ->get();
        if(count($finded_friends)>0)
        {
            $answer = $finded_friends;
        }
        else
        {
            $answer = 'No matching found';
        }
        return $answer;
    }
    public function send_request(Request $request)
    {
        $answer = Friend::firstOrCreate([
            'request_from_id' => Auth::user()->id,
            'request_to_id' => $request['friend_id']
        ]);
        return $answer;
    }
    public function accept_requests(Request $request)
    {
        Friend::where('id','=',$request['request_id'])
            ->update(['answer' => 1]);
        $accepted = DB::table('users')->select('users.id','name','answer')
            ->where('friends.id','=',$request['request_id'])
            ->join('friends', function($join)
            {
                $join->on('users.id', '=', 'request_from_id');
            })
            ->get();
        return $accepted;
    }
    public function remuve_friend(Request $request)
    {
        Friend::where('id','=', $request['request_id'])->delete();
        Chat::where('sender_id','=',$request['user_id'])
            ->where('recipient_id','=',Auth::user()->id)
            ->orWhere('sender_id','=',Auth::user()->id)
            ->where('recipient_id','=',$request['user_id'])
            ->update(['seen' => 1]);
        if(isset($request['check']))
        {
            $answer = User::where('id','=',$request['user_id'])->first();
        }
        else
        {
            $answer = 1;
        }
        return $answer;
    }
    public function deny_requests(Request $request)
    {
        Friend::where('id','=', $request['request'])->delete();
        return 1;
    }
    public function friends_online()
    {
        $friends = DB::table('users')->select('users.id','users.online')
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
        return $friends;
    }
    public function search_in_friend_list(Request $request)
    {
        $finded_friends = DB::table('users')->select('users.id','name','answer','users.online')
            ->where('users.name','like','%'.$request['friend'].'%')
            ->join('friends', function($join)
            {
                $join->on('users.id', '=', 'request_from_id')
                    ->where('request_to_id','=',Auth::user()->id)
                    ->where('answer','=',1)
                    ->orOn('users.id', '=', 'request_to_id')
                    ->Where('request_from_id','=',Auth::user()->id)
                    ->where('answer','=',1);
            })
            ->get();
        return $finded_friends;
    }
}
