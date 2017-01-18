<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function change_roles(User $user){
        foreach($user->roles as $role){
            if($role->name == "HeadAdmin"){
                return True;
            }
            return False;
        }
    }
}
