<?php

namespace App\Services;

use App\Models\Prediction;

class PredictionService
{

    /**
     * Creates a new prediction and returns it
     * @param $predictionData
     * @return Prediction
     */
    public function createPrediction($predictionData){
        return Prediction::create($predictionData);
    }

}
