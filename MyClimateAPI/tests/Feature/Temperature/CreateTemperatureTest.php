<?php

namespace Tests\Feature\Temperature;

use App\Models\Sensor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateTemperatureTest extends TestCase
{
    use RefreshDatabase;

    protected $url = 'api.temperatures.store';

    public function setUp(): void
    {
        parent::setUp();

        // Create sensor
        Sensor::factory()->create();
    }

    /**
     * As a user I want to register a new temperature
     *
     * @return void
     */
    public function test_register_temperature()
    {
        // Acting as a user who has a home with sensors
        Sanctum::actingAs(User::find(1));

        $requestBody = [
            'temperature' => fake()->randomFloat(min:-200,max: 400),
            'measured_at' => Carbon::now()->toString()
        ];

        $this->assertDatabaseCount('temperatures', 0);

        $response = $this->postJson(route($this->url, ['id' => Sensor::find(1)->id]), $requestBody);

        $response->assertStatus(201);

        $this->assertDatabaseCount('temperatures', 1);

        $response->assertJson([
            'data' => [
                'id' => 1,
                'temperature' => $requestBody['temperature'],
                'measured_at' => $requestBody['measured_at'],
                'sensor_id' => Sensor::find(1)->id
            ]
        ]);
    }


    /**
     * Validation error
     *
     * @return void
     */
    public function test_input_validation_error()
    {
        // Acting as a user who has a home with sensors
        Sanctum::actingAs(User::find(1));

        $requestBody = [
            'temperature' => 'temperature',
            'measured_at' => 'notADate'
        ];

        $this->assertDatabaseCount('temperatures', 0);

        $response = $this->postJson(route($this->url, ['id' => Sensor::find(1)->id]), $requestBody);

        $response->assertStatus(422);

        $this->assertDatabaseCount('temperatures', 0);

        $response->assertJsonStructure([
            'errors' => [
                'temperature',
                'measured_at'
            ]
        ]);
    }

    /**
     * Sensor could not be found
     *
     * @return void
     */
    public function test_sensor_not_found()
    {
        // Acting as a user who has a home with sensors
        Sanctum::actingAs(User::find(1));

        $requestBody = [
            'temperature' => fake()->randomFloat(min:-200,max: 400),
            'measured_at' => Carbon::now()->toString()
        ];

        $this->assertDatabaseCount('temperatures', 0);

        $response = $this->postJson(route($this->url, ['id' => 42340732048732]), $requestBody);

        $response->assertStatus(404);

        $this->assertDatabaseCount('temperatures', 0);
    }

    /**
     * User is Not owner of the sensor -> Forbidden
     *
     * @return void
     */
    public function test_user_is_not_owner_of_the_sensor()
    {
        // Acting as a user who hasn't a home with sensors
        Sanctum::actingAs(User::factory()->create());

        $requestBody = [
            'temperature' => fake()->randomFloat(min:-200,max: 400),
            'measured_at' => Carbon::now()->toString()
        ];

        $this->assertDatabaseCount('temperatures', 0);

        $response = $this->postJson(route($this->url, ['id' => Sensor::find(1)->id]), $requestBody);

        $response->assertStatus(403);

        $this->assertDatabaseCount('temperatures', 0);
    }

    /**
     * User is not authenticated
     *
     * @return void
     */
    public function test_user_is_not_authenticated()
    {
        // Acting as a user who is not authenticated

        $requestBody = [
            'temperature' => fake()->randomFloat(min:-200,max: 400),
            'measured_at' => Carbon::now()->toString()
        ];

        $this->assertDatabaseCount('temperatures', 0);

        $response = $this->postJson(route($this->url, ['id' => Sensor::find(1)->id]), $requestBody);

        $response->assertStatus(401);

        $this->assertDatabaseCount('temperatures', 0);
    }
}
