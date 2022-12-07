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

    /**
     * If found, returns a temperature that matches with the given id
     * @param int $id
     * @return mixed
     */
    public function findByIdOrFail(int $id){
        return Temperature::findOrFail($id);
    }

}
