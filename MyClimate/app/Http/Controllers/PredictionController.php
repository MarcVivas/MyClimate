<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePredictionRequest;
use App\Http\Resources\PredictionResource;
use App\Services\PredictionService;
use App\Services\TemperatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PredictionController extends Controller
{

    protected $predictionService;
    protected $temperatureService;
    public function __construct(PredictionService $predictionService, TemperatureService $temperatureService){
        $this->predictionService = $predictionService;
        $this->temperatureService = $temperatureService;
    }

    /**
     * @url POST /temperatures/{id}/predictions
     * @param CreatePredictionRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function store(CreatePredictionRequest $request, int $id){
        // User is authenticated --> Checked by sanctum middleware
        $user = Auth::user();

        $temperature = $this->temperatureService->findByIdOrFail($id);

        // Return 403 if the user is not the owner of the temperature
        abort_if($temperature->sensor->home->user_id !== $user->id, 403);

        // Get the data validated
        $validatedData = $request->validated();

        // Append sensor id
        $validatedData['sensor_id'] = $temperature->sensor->id;

        // Append temperature id
        $validatedData['temperature_id'] = $temperature->id;

        // Register the new prediction
        $prediction =  $this->predictionService->createPrediction($validatedData);

        // Return 201 and the new prediction
        return response()->json(['data' => new PredictionResource($prediction)], 201);
    }
}
