<?php

namespace App\Services;

use App\Models\Home;
use Illuminate\Database\Eloquent\Builder;

class HomeService
{

    /**
     * Stores a new home in the database
     * @param $homeData
     * @return mixed
     */
    public function createHome($homeData): mixed
    {
        return Home::create($homeData);
    }

    /**
     * Updates the given home
     * @return Home
     */
    public function updateHome(Home $home, $newHomeData){
        if(isset($newHomeData['name'])){
            $home->name = $newHomeData['name'];

        }
        if(isset($newHomeData['address'])){
            $home->address = $newHomeData['address'];
        }
        if(isset($newHomeData['description'])){
            $home->description = $newHomeData['description'];
        }

        $home->save();
        return $home;
    }

    /**
     * Deletes the given home and all the sensors it had
     * @param Home $home
     * @return void
     */
    public function deleteHome(Home $home){
        $home->delete();
    }


    /**
     * Returns all the users that match the filters.
     * @param $filters
     * @return Builder
     */
    public function applyFilters($filters){

        $houses = (new Home)->newQuery();

        if(isset($filters['id'])){
            $houses->where('id', $filters['id']);
        }

        if(isset($filters['user_id'])){
            $houses->where('user_id', $filters['user_id']);
        }

        if(isset($filters['address'])){
            $houses->where('address', 'like', '%'.$filters['address'].'%');
        }

        if(isset($filters['description'])){
            $houses->where('description', 'like', '%'.$filters['description'].'%');
        }

        return $houses;

    }

    /**
     * Returns a home that has the given id
     * @param int $id
     * @return Home
     */
    public function findByIdOrFail(int $id){
        return Home::findOrFail($id);
    }

}
