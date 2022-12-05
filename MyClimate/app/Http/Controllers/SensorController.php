<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSensorRequest;
use App\Http\Resources\SensorResource;
use App\Services\HomeService;
use App\Services\SensorService;
use Illuminate\Support\Facades\Auth;

class SensorController extends Controller
{
    protected $homeService;
    protected $sensorService;

    public function __construct(HomeService $homeService, SensorService $sensorService){
        $this->homeService = $homeService;
        $this->sensorService = $sensorService;
    }

    public function store(CreateSensorRequest $request, int $id){
        // User is authenticated --> Checked by sanctum middleware
        $user = Auth::user();

        $home = $this->homeService->findByIdOrFail($id); // If not found -> 404

        // Return 403 if the user is not the owner of the house
        abort_if($home->user_id !== $user->id, 403);

        $validatedData = $request->validated();

        // Append home id
        $validatedData['home_id'] = $home->id;

        $sensor = $this->sensorService->createSensor($validatedData);

        return response()->json(['data' => new SensorResource($sensor)], 201);

    }
}
