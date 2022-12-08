<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePredictionRequest;
use App\Http\Resources\PredictionResource;
use App\Services\PredictionService;
use App\Services\SensorService;
use App\Services\TemperatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PredictionController extends Controller
{

    protected $predictionService;
    protected $temperatureService;
    protected $sensorService;

    public function __construct(PredictionService $predictionService, TemperatureService $temperatureService, SensorService $sensorService){
        $this->predictionService = $predictionService;
        $this->temperatureService = $temperatureService;
        $this->sensorService = $sensorService;
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


    /**
     * Get all predictions
     * @url GET /sensors/{id}/predictions
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, int $id){
        // User is authenticated --> Checked by sanctum middleware
        $user = Auth::user();

        $sensor = $this->sensorService->findByIdOrFail($id);

        // Return 403 if the user is not the owner of the sensor
        abort_if($sensor->home->user_id !== $user->id, 403);

        // Pagination stuff
        $page = $request->get('page') !== null ? $request->get('page') : 1;
        $per_page = $request->get('perPage') !== null ? $request->get('perPage') : 10;


        return PredictionResource::collection($sensor->predictions()->paginate($per_page, ['*'], 'page', $page));

    }
}
