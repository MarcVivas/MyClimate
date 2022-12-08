<?php

namespace Database\Factories;

use App\Models\Sensor;
use App\Models\Temperature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prediction>
 */
class PredictionFactory extends Factory
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
            'temperature_id' => Temperature::factory(),
            'y_hat' => fake()->randomFloat(),
            'y_hat_lower' => fake()->randomFloat(),
            'y_hat_upper' => fake()->randomFloat(),
            'date' => Carbon::now()
        ];
    }
}
