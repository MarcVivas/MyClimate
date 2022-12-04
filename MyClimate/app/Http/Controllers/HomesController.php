<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHomeRequest;
use App\Http\Requests\UpdateHomeRequest;
use App\Http\Resources\HomeResource;
use App\Models\Home;
use App\Services\HomeService;
use Illuminate\Support\Facades\Auth;

class HomesController extends Controller
{

    protected $homeService;
    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;

    }


    /**
     * Creates a new home
     * @url POST /homes
     * @param CreateHomeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateHomeRequest $request){
        // User is authenticated --> Checked by sanctum middleware
        $user = Auth::user();

        // Get validated request data
        $validatedRequestData = $request->validated();

        // Append user id
        $validatedRequestData['user_id'] = $user->id;

        // Create a home whose owner will be the requester
        $home = $this->homeService->createHome($validatedRequestData);

        return response()->json(['data' => new HomeResource($home)], 201);

    }

    /**
     * Updates a home only if the authenticated user is the owner.
     * @param UpdateHomeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateHomeRequest $request, $id){
        // User is authenticated --> Checked by sanctum middleware
        $user = Auth::user();

        // Get the data validated
        $validatedRequestData = $request->validated();

        // Get home
        $home = Home::findOrFail($id);  // If not found -> 404

        // Only allowed to the owner of the house
        abort_if($home->user_id !== $user->id, 403);

        // Update the home
        $home = $this->homeService->updateHome($home, $validatedRequestData);

        return response()->json(['data' => new HomeResource($home)], 200);


    }
}
