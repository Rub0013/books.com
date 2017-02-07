<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Socialite;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Controllers\LanguageController;
use GuzzleHttp\Client;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public function showRegistrationForm()
    {
        $lang = new LanguageController();
        return view('auth.register');
    }
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
//            'g-recaptcha-response'=>'required'
        ]);
    }

    /**
     * Create a new user instance aft er avalid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
//        return User::create([
//            'name' => $data['name'],
//            'email' => $data['email'],
//            'birth_date' => $data['birth_date'],
//            'password' => bcrypt($data['password']),
//        ]);
        $token = $data['g-recaptcha-response'];
        if($token){
            $client = new Client();
            $response =  $client->post('https://www.google.com/recaptcha/api/siteverify',[
                'form_params' =>[
                    'secret' =>'6LcE9xIUAAAAAGZDmUyALqxSpMik_5Igh0RKD_CZ',
                    'response'=> $token,
                ]
            ]);

            $result = json_decode($response->getBody()->getContents());

            if($result->success){
                return User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'birth_date' => $data['birth_date'],
                    'password' => bcrypt($data['password']),
                ]);
            }else{
//                $response->error_code;
                return redirect()->back();

            }

        }else{
            return redirect()->back();
        }
    }
}
