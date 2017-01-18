<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Book;
use App\Booklike;
use DB;
use Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Redirect;

class LybraryController extends MainController
{
    public function books_count()
    {
        $books_count = Book::select('id')->count();
        return $books_count;
    }
    public function lybrary($local,$page=1,$books_per_page=4)
    {
        $lang = new LanguageController();
        $pages_count = ceil($this->books_count()/$books_per_page);
        $start = ($page-1) * $books_per_page;
        if(Auth::check())
        {
            $books = DB::table('books')->select('books.*','booklikes.liked_user_id')
                ->leftJoin('booklikes', function($join)
                {
                    $join->on('books.id', '=', 'booklikes.book_id')
                        ->where('booklikes.liked_user_id', '=', Auth::user()->id);
                })
                ->take($books_per_page)
                ->skip($start)
                ->orderBy('id','desc')
                ->get();
        }
        else
            {
                $books = Book::select('id','name','author','genre','image','user_id','likes')
                ->take($books_per_page)
                ->skip($start)
                ->orderBy('id','desc')
                ->get();
            }
        $previous = $page-1;
        if($previous<1)
        {
            $previous = 1;
        }
        $next = $page+1;
        if($next > $pages_count)
        {
            $next = $pages_count;
        }
        $array = array('books' => $books,
            'previous' => $previous,
            'next' => $next,
            'total_notes_count' => $this->total_notes_count(),
            'total_requests' => $this->total_requests(),
            'books_count' => $this->books_count(),
            'books_per_page' => $books_per_page,
            'pages_count' => $pages_count);
        return view('lybrary',$array);
    }
    public function add_books(Request $request)
    {
        $lang = new LanguageController();
        if($request->isMethod('get'))
        {
            return view('add_books');
        }
        if($request->isMethod('post'))
        {
            if(Gate::denies('add',new Book)){
                return 'notadded';
            }
            $rules = [
                'book_name' => 'required',
                'author_name' => 'required',
                'genre' => 'required',
                'user_id' => 'required',
                'image' => 'required',
            ];
            $this->validate($request, $rules);
            $filename = time().'.'.$request['image']->getClientOriginalExtension();
            Storage::disk('public')->put($filename,File::get($request['image']));
            Book::create([
                'name' => $request['book_name'],
                'author' => $request['author_name'],
                'genre' => $request['genre'],
                'user_id' => $request['user_id'],
                'image' => $filename
            ]);
            return 'added';
        }
    }
    public function updating(Request $request)
    {
        $rules = [
            'new_name' => 'required',
            'new_author' => 'required',
            'new_genre' => 'required',
        ];
        $this->validate($request, $rules);
        Book::where('id','=',$request['book_id'])
            ->update([
                'name' => $request['new_name'],
                'author' => $request['new_author'],
                'genre' => $request['new_genre'],
            ]);
        if(isset($request['new_image']))
        {
            $filename = time().'.'.$request['new_image']->getClientOriginalExtension();
            Storage::disk('public')->put($filename,File::get($request['new_image']));
            Storage::disk('public')->delete($request['old_image']);
            Book::where('id','=',$request['book_id'])
                ->update([
                    'image' => $filename,
                ]);
        }
        $updated_book = Book::find($request['book_id']);
        return $updated_book;
    }
    public  function  delete($id,$name)
    {
//        Booklike::where('book_id','=', $id)->delete();
        Book::where('id','=', $id)->delete();
        Storage::disk('public')->delete($name);
        return Redirect::back();
    }
    public function if_liked(Request $request)
    {
        $liked = Booklike::select('id')
            ->where('book_id','=',$request['id_book'])
            ->where('liked_user_id','=',$request['id_user'])
            ->get();
        return $liked;
    }
    public function like(Request $request)
    {
        if(!isset($this->if_liked($request)[0]))
        {
            Booklike::create([
                'book_id' =>  $request['id_book'],
                'liked_user_id' => $request['id_user'],
                'like' =>  1
            ]);
        }
        else
        {
            Booklike::where('book_id', '=', $request['id_book'])
                ->where('liked_user_id', '=',$request['id_user'])
                ->delete();
        }
        $likes = Booklike::select('like')
            ->where('book_id','=',$request['id_book'])
            ->get();
        $answer = count($likes);
        Book::where('id','=', $request['id_book'])
            ->update(['likes' => $answer]);
        return $answer;
    }
}
