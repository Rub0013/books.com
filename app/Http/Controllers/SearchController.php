<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use Auth;
use App\Chat;
use DB;

class SearchController extends MainController
{
    public function search_show(Request $request)
    {
        if(Auth::check())
        {
            $books = DB::table('books')->select('books.*','booklikes.liked_user_id')
                ->where('name', 'like', '%'.preg_quote($request['book_name']).'%')
                ->leftJoin('booklikes', function($join)
                {
                    $join->on('books.id', '=', 'booklikes.book_id')
                        ->where('booklikes.liked_user_id', '=', Auth::user()->id);
                })
                ->orderBy('id','desc')
                ->get();
        }
        else
        {
            $books = Book::select('id','name','author','genre','image','user_id','likes')
                ->where('name', 'like', '%'.preg_quote($request['book_name']).'%')
                ->orderBy('id','desc')
                ->get();
        }
        if(count($books)>0)
        {
            $array = array('books' => $books);
            return $array;
        }
        else
            {
                $answer = 'No matching found';
                return $answer;
            }
    }
    public function search_show_one($local,$id)
    {
        $lang = new LanguageController();
        if(Auth::check())
        {
            $books = DB::table('books')->select('books.*','booklikes.liked_user_id')->where('books.id','=',$id)
                ->leftJoin('booklikes', function($join)
                {
                    $join->on('books.id', '=', 'booklikes.book_id')
                        ->where('booklikes.liked_user_id', '=', Auth::user()->id);
                })
                ->get();
        }
        else
        {
            $books = Book::select('id','name','author','genre','image','user_id','likes')
                ->where('books.id','=',$id)
                ->get();
        }
        $array = array('books' => $books,
        'total_notes_count' => $this->total_notes_count(),
        'total_requests' => $this->total_requests());
        return view('finded_book',$array);
    }
}
