<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Redirect;
use App;

class LanguageController extends Controller
{
    public  $language;
    public function __construct()
    {
        $this->language = request()->segment(1);
        App::setLocale(request()->segment(1));
    }
}
