<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    use HasFactory;
    public $timestamps = false;

    public $fillable = [
        'date',
        'sensor_id',
        'temperature_id',
        'y_hat',
        'y_hat_lower',
        'y_hat_upper'
    ];

    /**
     * A prediction belongs to a sensor
     * @return BelongsTo
     */
    public function sensor(){
        return $this->belongsTo(Sensor::class);
    }

    /**
     * A prediction belongs to a temperature
     * @return BelongsTo
     */
    public function temperature(){
        return $this->belongsTo(Temperature::class);
    }


}
