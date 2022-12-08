<?php

namespace Tests\Feature\Prediction;

use App\Models\Temperature;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreatePredictionTest extends TestCase
{
    use RefreshDatabase;

    protected $url = 'api.predictions.store';

    public function setUp(): void
    {
        parent::setUp();

        Temperature::factory()->create();
    }

    /**
     * As a user I want to register a new prediction
     *
     * @return void
     */
    public function test_register_prediction()
    {
        // Acting as a user who has measured temperatures
        Sanctum::actingAs(User::find(1));

        $requestBody = [
            'date' => Carbon::now()->toDateTimeString(),
            'y_hat' => fake()->randomFloat(min:-200, max:400),
            'y_hat_upper' => fake()->randomFloat(min:-200, max:400),
            'y_hat_lower' => fake()->randomFloat(min:-200, max:400),
        ];

        $this->assertDatabaseCount('predictions', 0);

        $response = $this->postJson(route($this->url, ['id' => Temperature::find(1)->id]), $requestBody);

        $response->assertStatus(201);

        $this->assertDatabaseCount('predictions', 1);

        $response->assertJson([
            'data' => [
                'sensor_id' => 1,
                'temperature_id' => 1,
                'y_hat' => $requestBody['y_hat'],
                'y_hat_lower' => $requestBody['y_hat_lower'],
                'y_hat_upper' => $requestBody['y_hat_upper'],
                'date' => $requestBody['date']
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
        // Acting as a user who has measured temperatures
        Sanctum::actingAs(User::find(1));

        $requestBody = [
            'date' => 'date',
            'y_hat' => 'temp',
            'y_hat_upper' => 'temp',
            'y_hat_lower' => 'temp',
        ];

        $this->assertDatabaseCount('predictions', 0);

        $response = $this->postJson(route($this->url, ['id' => Temperature::find(1)->id]), $requestBody);

        $response->assertStatus(422);

        $this->assertDatabaseCount('predictions', 0);

        $response->assertJsonStructure([
            'errors' => [
                'date',
                'y_hat',
                'y_hat_lower',
                'y_hat_upper'
            ]
        ]);
    }

    /**
     * Temperature could not be found
     *
     * @return void
     */
    public function test_temperature_not_found()
    {
        // Acting as a user who has measured temperatures
        Sanctum::actingAs(User::find(1));

        $requestBody = [
            'date' => Carbon::now()->toDateTimeString(),
            'y_hat' => fake()->randomFloat(min:-200, max:400),
            'y_hat_upper' => fake()->randomFloat(min:-200, max:400),
            'y_hat_lower' => fake()->randomFloat(min:-200, max:400),
        ];

        $this->assertDatabaseCount('predictions', 0);

        $response = $this->postJson(route($this->url, ['id' => 42340732048732]), $requestBody);

        $response->assertStatus(404);

        $this->assertDatabaseCount('predictions', 0);
    }

    /**
     * User is Not owner of the measured temperature -> Forbidden
     *
     * @return void
     */
    public function test_user_is_not_owner_of_the_measured_temperature()
    {
        // Acting as a user who hasn't a home with sensors and temperatures
        Sanctum::actingAs(User::factory()->create());

        $requestBody = [
            'date' => Carbon::now()->toDateTimeString(),
            'y_hat' => fake()->randomFloat(min:-200, max:400),
            'y_hat_upper' => fake()->randomFloat(min:-200, max:400),
            'y_hat_lower' => fake()->randomFloat(min:-200, max:400),
        ];

        $this->assertDatabaseCount('predictions', 0);

        $response = $this->postJson(route($this->url, ['id' => Temperature::find(1)->id]), $requestBody);

        $response->assertStatus(403);

        $this->assertDatabaseCount('predictions', 0);
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
            'date' => Carbon::now()->toDateTimeString(),
            'y_hat' => fake()->randomFloat(min:-200, max:400),
            'y_hat_upper' => fake()->randomFloat(min:-200, max:400),
            'y_hat_lower' => fake()->randomFloat(min:-200, max:400),
        ];

        $this->assertDatabaseCount('predictions', 0);

        $response = $this->postJson(route($this->url, ['id' => Temperature::find(1)->id]), $requestBody);

        $response->assertStatus(401);

        $this->assertDatabaseCount('predictions', 0);
    }
}
