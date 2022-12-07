<?php

use App\Http\Controllers\TemperatureController;
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
Route::middleware(['auth:sanctum'])->group(function () {

    // Create a temperature
    Route::post('sensors/{id}/temperatures', [TemperatureController::class, 'store'])
        ->name('api.temperatures.store');


    // Get all temperatures of a sensor
    Route::get('sensors/{id}/temperatures', [TemperatureController::class, 'index'])
        ->name('api.temperatures.index');


});
