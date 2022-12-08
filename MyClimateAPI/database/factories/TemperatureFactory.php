<?php

namespace Database\Factories;

use App\Models\Sensor;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Temperature>
 */
class TemperatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'sensor_id' => Sensor::factory(),
            'temperature' => fake()->randomFloat(),
            'measured_at' => Carbon::now()
        ];
    }
}
