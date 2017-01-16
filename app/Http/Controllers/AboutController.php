<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Chat;
use App\Language;
use Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class AboutController extends MainController
{
    public function about()
    {
        $lang = new LanguageController();
        $about = Language::select('about')
            ->where('lang','=',App::getLocale())
            ->first();
        $array = array('total_notes_count' => $this->total_notes_count(),
            'total_requests' => $this->total_requests(),
            'about' => $about);
        return view('about',$array);
    }
}
