<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\App;

class LoginController extends Controller
{
    public function logout(Request $request) {
        $currentuser = Auth::user()->id;
        DB::table('users')
            ->where('id', $currentuser)
            ->update(['online' => 0]);
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect('/');
    }
    public function showLoginForm()
    {
        $lang = new LanguageController();
        return view('auth.login');
    }

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public function redirectPath()
    {
        $lang = new LanguageController();
        return '/'.App::getLocale().'/friends';
    }
    protected $redirectTo = '/';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
}
