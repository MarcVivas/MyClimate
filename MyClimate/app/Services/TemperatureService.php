<?php

namespace App\Services;

use App\Models\Temperature;

class TemperatureService
{

    /**
     * Creates a new temperature and returns it
     * @param $temperatureData
     * @return Temperature
     */
    public function createTemperature($temperatureData){
        return Temperature::create($temperatureData);
    }

}
