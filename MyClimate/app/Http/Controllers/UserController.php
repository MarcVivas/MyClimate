<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

    /**
     * Logins a user
     * @url POST /auth/login
     * @return AuthResource
     */
    public function login(LoginRequest $request){

        // Get the request data
        $validatedData = $request->validated();

        // Get the user
        $user = $this->userService->findByUserNameOrFail($validatedData['username']);

        // Return 403 if the given password is not correct
        abort_if(!Hash::check($validatedData['password'], $user->password), 403);

        return new AuthResource($user->createToken($validatedData['username'])->plainTextToken);
    }
}
