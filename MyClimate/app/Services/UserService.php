<?php

namespace App\Services;

use App\Models\User;

class UserService
{

    /**
     * Finds and returns a user if it is found.
     * If the user does not exist, the function returns a 404
     * @param $username
     * @return User
     */
    public function findByUserNameOrFail($username){
        return User::where('username', $username)->firstOrFail();
    }

}
