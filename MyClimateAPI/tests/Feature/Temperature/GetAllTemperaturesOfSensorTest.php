<?php

namespace Tests\Feature\Temperature;

use App\Models\Sensor;
use App\Models\Temperature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetAllTemperaturesOfSensorTest extends TestCase
{
    use RefreshDatabase;

    protected $url = 'api.temperatures.index';

    public function setUp(): void
    {
        parent::setUp();

        $sensor = Sensor::factory()->create();

        // Create 2 temperatures measured by the previous sensor
        Temperature::factory(2)->create([
            'sensor_id' => $sensor->id
        ]);

    }

    /**
     * User is not authenticated
     *
     * @return void
     */
    public function test_user_is_not_authenticated()
    {
        // Acting as a user who is not authenticated
        $response = $this->getJson(route($this->url, ['id' => Sensor::find(1)->id]));
        $response->assertStatus(401);
    }

    /**
     * User is not the owner of the sensor
     *
     * @return void
     */
    public function test_user_is_not_owner_of_the_sensor()
    {
        // Acting as a user who is not owner of the sensor
        Sanctum::actingAs(User::factory()->create());
        $response = $this->getJson(route($this->url, ['id' => Sensor::find(1)->id]));
        $response->assertStatus(403);
    }

    /**
     * Sensor not found
     *
     * @return void
     */
    public function test_sensor_not_found()
    {
        // Acting as a user who is the owner of the sensor
        Sanctum::actingAs(User::find(1));
        $response = $this->getJson(route($this->url, ['id' => 713298731927398]));
        $response->assertStatus(404);
    }


    /**
     * As a valid user, I want to get all the temperatures of a sensor
     *
     * @return void
     */
    public function test_get_all_temperatures_of_a_sensor()
    {
        // Acting as a user who is the owner of the sensor
        Sanctum::actingAs(User::find(1));
        $response = $this->getJson(route($this->url, ['id' => Sensor::find(1)->id]));
        $response->assertStatus(200);

        $sensor = Sensor::find(1);
        $temp1 = Temperature::find(1);
        $temp2 = Temperature::find(2);

        $response->assertJson([
            'data' => [
                0 => [
                    'id' => $temp1->id,
                    'temperature' => $temp1->temperature,
                    'measured_at' => $temp1->measured_at,
                    'sensor_id' => $sensor->id
                ],
                1 => [
                    'id' => $temp2->id,
                    'temperature' => $temp2->temperature,
                    'measured_at' => $temp2->measured_at,
                    'sensor_id' => $sensor->id
                ]
            ]
        ]);
    }
}
