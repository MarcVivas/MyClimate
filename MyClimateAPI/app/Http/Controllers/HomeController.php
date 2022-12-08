<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHomeRequest;
use App\Http\Requests\UpdateHomeRequest;
use App\Http\Resources\HomeResource;
use App\Models\Home;
use App\Services\HomeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
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
     * @return JsonResponse
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
     * @url PATCH /homes/{id}
     * @param UpdateHomeRequest $request
     * @param $id
     * @return JsonResponse
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

    /**
     * Deletes a home and its sensors
     * @url DELETE /homes/{id}
     * @param $id
     * @return Response
     */
    public function destroy($id){
        // User is authenticated --> Checked by sanctum middleware
        $user = Auth::user();

        // Get home
        $home = Home::findOrFail($id);  // If not found -> 404

        // Only allowed to the owner of the house
        abort_if($home->user_id !== $user->id, 403);

        // Delete the house
        $this->homeService->deleteHome($home);

        return response()->noContent();        // 204
    }


    /**
     * Get all homes filtered by the given query parameters
     * @url GET /homes
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request){

        // Pagination stuff
        $page = $request->get('page') !== null ? $request->get('page') : 1;
        $per_page = $request->get('perPage') !== null ? $request->get('perPage') : 10;

        $houses = $this->homeService->applyFilters($request);

        return HomeResource::collection($houses->paginate($per_page, ['*'], 'page', $page));
    }


    /**
     * Get a home
     * @url GET /homes/{id}
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id){

        $home = $this->homeService->findByIdOrFail($id);    // If not found -> 404

        return response()->json(['data' => new HomeResource($home)], 200);
    }



    /**
     * Get all homes of the authenticated user
     * @url GET /homes
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function getUserHomes(Request $request){
        // User is authenticated --> Checked by sanctum middleware
        $user = Auth::user();

        // Pagination stuff
        $page = $request->get('page') !== null ? $request->get('page') : 1;
        $per_page = $request->get('perPage') !== null ? $request->get('perPage') : 10;


        return HomeResource::collection($user->homes()->paginate($per_page, ['*'], 'page', $page));
    }

}
