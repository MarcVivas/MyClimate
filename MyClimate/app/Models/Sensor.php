<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    use HasFactory;
    public $timestamps = false;


    /**
     * A sensor is located inside a specific house
     * @return BelongsTo
     */
    public function home(){
        return $this->belongsTo(Home::class);
    }

    /**
     * A sensor measures many temperatures
     * @return HasMany
     */
    public function temperatures(){
        return $this->hasMany(Temperature::class);
    }

    public function predictions(){
        return $this->hasMany(Prediction::class);
    }


}
