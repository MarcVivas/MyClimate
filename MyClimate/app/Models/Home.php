<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Home extends Model
{
    use HasFactory;
    public $timestamps = false;


    /**
     * A home can have many sensors
     * @return HasMany
     */
    public function sensors(){
        return $this->hasMany(Sensor::class);
    }

    /**
     * A home belongs to one or zero users
     * @return HasMany
     */
    public function user(){
        return $this->hasMany(User::class);
    }

}
