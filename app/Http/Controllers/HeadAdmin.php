<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gate;
use Auth;
use App\User;
use App\Role;
use App\RoleUser;
use App\Book;
use Illuminate\Support\Facades\Session;

class HeadAdmin extends Controller
{
    public function view(){
        if(Gate::denies('change_roles',new Role)){
            return view('haveNoPermission');
        }else{
            return view("headadmin");
        }
    }

    public function all_user_roles(){
        if(Gate::denies('change_roles',new Role)){
            return view('haveNoPermission');
        }else{
            $auth_id = Auth::id();
            $users = User::select('users.id','users.name','role_user.role_id','roles.name as roles_name')
                ->leftJoin('role_user', function ($join) {
                    $join->on('users.id', '=', 'role_user.user_id');
                })->leftJoin('roles', function ($join) {
                    $join->on('role_user.role_id', '=', 'roles.id');
                })->where('users.id','<>',$auth_id)
                ->get();


            $roles = Role::all();

            return view("all_users_roles",['users'=>$users,'roles'=>$roles]);

//            SELECT users.id,users.name,role_user.role_id,roles.name FROM `users` left join role_user on users.id = role_user.user_id left join roles on role_user.role_id = roles.id
        }
    }

    public function change_user_roles(Request $request){
        if(Gate::denies('change_roles',new Role)){
            return view('haveNoPermission');
        }else{
            $user_id = $request->id;
            $role_name = $request->role;
            $role_id_obj = Role::select('id')->where('name',$role_name)->first();
            $role_id = $role_id_obj->id;


            if ($role_name == "user"){
                RoleUser::where('user_id',$user_id)->delete();
                return 1;
            }else{
                RoleUser::updateOrCreate(['user_id'=>$user_id],['role_id'=>$role_id]);
                return 1;
            }

            return 0;

        }
    }


    public function login_as(Request $request){
        $general_auth_id = Auth()->id();
        Session::put('general_id', $general_auth_id);
        Auth::logout();
        Auth::loginUsingId($request->id);
        return 1;
    }

    public function logout_back(Request $request){
        $general_auth_id = Session::get('general_id');
        session()->forget('general_id');
        Auth::logout();
        Auth::loginUsingId($general_auth_id);
        return redirect('/en/headadmin');
    }

}
