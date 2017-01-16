<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class ProfileController extends MainController
{
    public function show()
    {
        $lang = new LanguageController();
        $my_profile = User::find(Auth::user()->id);
        $array = array('total_notes_count' => $this->total_notes_count(),
            'total_requests' => $this->total_requests(),
            'info' => $my_profile
            );
        return view('profile',$array);
    }
    public function update(Request $request)
    {
        if($request['birth'])
        {
            User::where('id','=',Auth::user()->id)
                ->update(['birth_date' => $request['birth']]);
        }
        if($request['email'])
        {
            User::where('id','=',Auth::user()->id)
                ->update(['email' => $request['email']]);
        }
        $answer = User::find(Auth::user()->id);
        return $answer;
    }
}
