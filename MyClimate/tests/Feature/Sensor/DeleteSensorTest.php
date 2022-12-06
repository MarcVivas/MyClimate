<?php

namespace Tests\Feature\Sensor;

use App\Models\Prediction;
use App\Models\Sensor;
use App\Models\Temperature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteSensorTest extends TestCase
{
    use RefreshDatabase;

    protected $url = 'api.sensors.destroy';

    public function setUp(): void
    {
        parent::setUp();

        $sensor = Sensor::factory()->create();

        $temperature = Temperature::factory()->create([
            'sensor_id' => $sensor->id
        ]);

        Prediction::factory()->create([
            'sensor_id' => $sensor->id,
            'temperature_id' => $temperature->id
        ]);
    }

    /**
     * User is not authenticated -> Requires authentication
     *
     * @return void
     */
    public function test_user_is_not_authenticated()
    {
        // Acting as a user who is not authenticated

        $response = $this->deleteJson(route($this->url, ['id' => Sensor::find(1)->id]));

        $response->assertStatus(401);
    }

    /**
     * User is not the owner of the sensor -> Forbidden
     *
     * @return void
     */
    public function test_user_is_not_the_owner_of_the_sensor()
    {
        // Acting as a user who does not have sensors
        Sanctum::actingAs(User::factory()->create());

        $response = $this->deleteJson(route($this->url, ['id' => Sensor::find(1)->id]));

        $response->assertStatus(403);
    }


    /**
     * Sensor not found
     *
     * @return void
     */
    public function test_sensor_not_found()
    {
        // Acting as a user who has sensors
        Sanctum::actingAs(User::find(1));

        $response = $this->deleteJson(route($this->url, ['id' => 4234324324324]));

        $response->assertStatus(404);
    }


    /**
     * As a user who has sensors, I want to delete one of them
     *
     * @return void
     */
    public function test_delete_one_of_my_sensors()
    {
        // Acting as a user who has sensors
        Sanctum::actingAs(User::find(1));

        $this->assertDatabaseCount('homes', 1);
        $this->assertDatabaseCount('sensors', 1);
        $this->assertDatabaseCount('predictions', 1);
        $this->assertDatabaseCount('temperatures', 1);

        $response = $this->deleteJson(route($this->url, ['id' => Sensor::find(1)->id]));

        $response->assertStatus(204);

        $this->assertDatabaseCount('homes', 1);
        $this->assertDatabaseCount('sensors', 0);
        $this->assertDatabaseCount('predictions', 0);
        $this->assertDatabaseCount('temperatures', 0);
    }
}
