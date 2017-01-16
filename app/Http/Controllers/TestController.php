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

class TestController extends MainController
{
  public function testing(Request $request)
  {
      $url = $request->url();
      echo $url;
  }
}

