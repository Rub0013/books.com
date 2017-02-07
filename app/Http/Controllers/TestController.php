<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use App\Book;
use App\Booklike;
use App\User;
use DB;
use Auth;
use Exception;
use App\Chat;
use Illuminate\Support\Facades\App;
use PHPUnit_Runner_Version;

class TestController extends MainController
{
  public function testing(Request $request)
  {
      $friends = User::select('users.id','users.online')
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
      $arr = [];
      foreach ($friends as $friend)
      {
          $arr[] = [
              $friend['id']=>$friend->isOnline()
          ];
      }
      dd($arr);
  }
}

