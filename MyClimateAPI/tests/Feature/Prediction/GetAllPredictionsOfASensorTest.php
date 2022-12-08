<?php

namespace Tests\Feature\Prediction;

use App\Models\Prediction;
use App\Models\Sensor;
use App\Models\Temperature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetAllPredictionsOfASensorTest extends TestCase
{
    use RefreshDatabase;

    protected $url = 'api.predictions.index';

    public function setUp(): void
    {
        parent::setUp();

        $sensor = Sensor::factory()->create();

        // Create 2 predictions that belong to a sensor.
        Prediction::factory(2)->create([
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
     * As a valid user, I want to get all the predictions of a sensor
     *
     * @return void
     */
    public function test_get_all_predictions_of_a_sensor()
    {
        // Acting as a user who is the owner of the sensor
        Sanctum::actingAs(User::find(1));
        $response = $this->getJson(route($this->url, ['id' => Sensor::find(1)->id]));
        $response->assertStatus(200);

        $sensor = Sensor::find(1);
        $pred1 = Prediction::where('temperature_id', 1)->first();
        $pred2 = Prediction::where('temperature_id', 2)->first();

        $response->assertJson([
            'data' => [
                0 => [
                    'sensor_id' => $sensor->id,
                    'temperature_id' => 1,
                    'date' => $pred1->date,
                    'y_hat' => $pred1->y_hat,
                    'y_hat_lower' => $pred1->y_hat_lower,
                    'y_hat_upper' => $pred1->y_hat_upper
                ],
                1 => [
                    'sensor_id' => $sensor->id,
                    'temperature_id' => 2,
                    'date' => $pred2->date,
                    'y_hat' => $pred2->y_hat,
                    'y_hat_lower' => $pred2->y_hat_lower,
                    'y_hat_upper' => $pred2->y_hat_upper
                ]
            ]
        ]);
    }
}
