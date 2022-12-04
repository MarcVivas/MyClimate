<?php

use App\Http\Controllers\HomesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Home
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware(['auth:sanctum'])->group(function () {

    // Create a new home
    Route::post('homes', [HomesController::class, 'store'])
        ->name('api.homes.store');

    // Update a home
    Route::patch('homes/{id}', [HomesController::class, 'update'])
        ->name('api.homes.update');

});

