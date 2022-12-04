<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

    /**
     * Creates a new user and returns it
     * @param $userData
     * @return User
     */
    public function createUser($userData){
        return User::create([
            'username' => $userData['username'],
            'password' => Hash::make($userData['password'])  // Encrypted
        ]);
    }

}
