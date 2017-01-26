<?php

namespace App\Policies;

use App\User;
use App\Book;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function add(User $user){
        foreach($user->roles as $role){
            if($role->name == "Admin" || $role->name == "HeadAdmin"){
                return True;
            }
            return False;
        }
    }

//    public function update(User $user, Book $book){
//        foreach($user->roles as $role){
//            if($role->name == "Admin" || $role->name == "HeadAdmin"){
//                if($role->id == $book->user_id){
//                    return True;
//                }
//            }
//            return False;
//        }
//    }
}
