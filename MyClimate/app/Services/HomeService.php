<?php

namespace App\Services;

use App\Models\Home;
use function Symfony\Component\String\s;

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

}
