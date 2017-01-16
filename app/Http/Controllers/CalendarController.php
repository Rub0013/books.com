<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Language;
use Auth;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\LanguageController;
use App\User;

class CalendarController extends MainController
{
    public function calendar()
    {
        $lang = new LanguageController();
        $friends1 = User::select('users.name as title','users.birth_date as start')
            ->where('birth_date','<>',null)
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
        $year = date("Y");
        $friends = array();
        foreach ($friends1 as $item)
        {
            $friend['start'] = explode("-",$item->start);
            $friend['start'][0] = $year;
            $friend['start'] = join('-',$friend['start']);
            $friend['title'] = $item->title;
            $friends[]=$friend;
        }
        $array = array('total_notes_count' => $this->total_notes_count(),
            'total_requests' => $this->total_requests(),
            'data' => $friends,
        );
        return view('calendar',$array);
    }
}
