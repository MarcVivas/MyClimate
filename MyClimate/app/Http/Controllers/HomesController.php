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
        // Get request data
        $validatedRequestData = $request->validated();

        // Check if the user is authenticated
        if(Auth::check()){
            // User is authenticated
            $user = Auth::user();

            // Append user id
            $validatedRequestData['user_id'] = $user->id;
        }

        $home = $this->homeService->createHome($validatedRequestData);

        return new HomeResource($home);

    }
}
