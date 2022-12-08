<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// User login
Route::post('user/login', [UserController::class, 'login'])
    ->name('api.users.login');

// User register
Route::post('user/register', [UserController::class, 'register'])
    ->name('api.users.register');
