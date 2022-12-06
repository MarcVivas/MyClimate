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

    /**
     * Finds a sensor by id or returns 404 if it cannot be found
     * @param int $id
     * @return mixed
     */
    public function findByIdOrFail(int $id){
        return Sensor::findOrFail($id);
    }

    /**
     * Deletes the given sensor and also its predictions and temperatures.
     * @param Sensor $sensor
     * @return void
     */
    public function deleteSensor(Sensor $sensor){
        $sensor->delete();
    }

}
