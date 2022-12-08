<?php

namespace Tests\Feature\Sensor;

use App\Models\Home;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateSensorTest extends TestCase
{
    use RefreshDatabase;

    protected $url = 'api.sensors.store';

    public function setUp(): void
    {
        parent::setUp();

        Home::factory()->create();
    }

    /**
     * As a user with a house, I want to create a new sensor.
     *
     * @return void
     */
    public function test_create_a_new_sensor()
    {
        // Acting as an authenticated user with a house
        Sanctum::actingAs(User::find(1));

        $home = Home::find(1);

        $requestBody = [
            'room' => "Kitchen"
        ];

        $response = $this->postJson(route($this->url, ['id' => $home->id]), $requestBody);

        $response->assertStatus(201);

        $this->assertDatabaseCount("sensors", 1);

        $response->assertJson([
            'data' => [
                'id' => 1,
                'room' => $requestBody['room'],
                'home_id' => $home->id
            ]
        ]);
    }

    /**
     * Room not provided
     *
     * @return void
     */
    public function test_room_is_not_given()
    {
        // Acting as an authenticated user with a house
        Sanctum::actingAs(User::find(1));

        $home = Home::find(1);

        $requestBody = [

        ];

        $response = $this->postJson(route($this->url, ['id' => $home->id]), $requestBody);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors' => [
                'room'
            ]
        ]);
    }

    /**
     * Room is too long
     *
     * @return void
     */
    public function test_room_is_too_long()
    {
        // Acting as an authenticated user with a house
        Sanctum::actingAs(User::find(1));

        $home = Home::find(1);

        $requestBody = [
            "room" => str_repeat('Kitchen', 432)
        ];

        $response = $this->postJson(route($this->url, ['id' => $home->id]), $requestBody);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors' => [
                'room'
            ]
        ]);
    }


    /**
     * A user tries to create a sensor, but he does not own the house.
     *
     * @return void
     */
    public function test_user_is_not_the_owner_of_the_house()
    {
        // Acting as an authenticated user without a house
        Sanctum::actingAs(User::factory()->create());

        $home = Home::find(1);

        $requestBody = [
            "room" => 'Kitchen'
        ];

        $response = $this->postJson(route($this->url, ['id' => $home->id]), $requestBody);

        $response->assertStatus(403);
    }

    /**
     * A user tries to create a sensor, but the house does not exist.
     *
     * @return void
     */
    public function test_house_could_not_be_found()
    {
        // Acting as an authenticated user, owner of a house
        Sanctum::actingAs(User::find(1));

        $home = Home::find(1);

        $requestBody = [
            "room" => 'Kitchen'
        ];

        $response = $this->postJson(route($this->url, ['id' => 432847329847]), $requestBody);

        $response->assertStatus(404);
    }

    /**
     * User is not authenticated
     *
     * @return void
     */
    public function test_user_is_not_authenticated()
    {
        // Acting as a non-authenticated user

        $home = Home::find(1);

        $requestBody = [
            "room" => 'Kitchen'
        ];

        $response = $this->postJson(route($this->url, ['id' => $home->id]), $requestBody);

        $response->assertStatus(401);
    }


}
