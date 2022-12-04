<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHomeRequest;
use App\Http\Resources\HomeResource;
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
     * @return HomeResource
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

        return new HomeResource($home);

    }
}
