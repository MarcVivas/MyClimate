<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Temperature extends Model
{
    use HasFactory;
    public $timestamps = false;


    /**
     * A temperature is measured by a sensor
     * @return BelongsTo
     */
    public function sensor(){
        return $this->belongsTo(Sensor::class);
    }


    /**
     * A temperature is measured by a sensor
     * @return HasOne
     */
    public function prediction(){
        return $this->hasOne(Prediction::class);
    }

}
