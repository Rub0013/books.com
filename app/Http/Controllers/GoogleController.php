<?php

namespace App\Http\Controllers;

use Socialite;
use App\User;
use App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class GoogleController extends MainController
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        $lang = new LanguageController();
        Session::put('locale',App::getLocale());
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        App::setLocale(Session::get('locale'));
        $user = Socialite::driver('google')->user();
        $loged_user = User::where('google_id', '=', $user->id)->first();
        if (!isset($loged_user)) {
            $logged = User::firstOrCreate([
                'google_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
            auth()->login($logged);
        }
        else
        {
            auth()->login($loged_user);
        }
        return Redirect::route('friends', array('local' => App::getLocale()));
    }
}
