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

}
