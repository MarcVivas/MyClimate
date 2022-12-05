<?php

namespace App\Services;

use App\Models\Sensor;

class SensorService
{

    /**
     * Creates a new sensor and returns it
     * @param $sensorData
     * @return Sensor
     */
    public function createSensor($sensorData){
        return Sensor::create($sensorData);
    }

}
