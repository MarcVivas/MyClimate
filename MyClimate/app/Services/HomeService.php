<?php

namespace App\Services;

use App\Models\Home;

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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyFilters($filters){

        $houses = (new Home)->newQuery();

        if(isset($filters['id'])){
            $houses->where('id', $filters['id']);
        }

        if(isset($filters['address'])){
            $houses->where('address', 'like', '%'.$filters['address'].'%');
        }

        if(isset($filters['description'])){
            $houses->where('description', 'like', '%'.$filters['description'].'%');
        }

        return $houses;

    }

}
