<?php

use App\Http\Controllers\SensorController;
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

    // Create a new sensor
    Route::post('homes/{id}/sensors', [SensorController::class, 'store'])
        ->name('api.sensors.store');

    // Get all sensors
    Route::get('homes/{id}/sensors', [SensorController::class, 'index'])
        ->name('api.sensors.index');

    // Delete a sensor
    Route::delete('sensors/{id}', [SensorController::class, 'destroy'])
        ->name('api.sensors.destroy');

});
