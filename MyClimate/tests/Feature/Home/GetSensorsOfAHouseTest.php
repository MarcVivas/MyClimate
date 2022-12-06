<?php

namespace Tests\Feature\Home;

use App\Models\Home;
use App\Models\Sensor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetSensorsOfAHouseTest extends TestCase
{
    use RefreshDatabase;

    protected $url = 'api.sensors.index';

    public function setUp(): void
    {
        parent::setUp();

        $home = Home::factory()->create();

        // Create 2 sensors
        Sensor::factory(2)->create(['home_id' => $home->id]);

        // Create another sensor that does not belong to the first created house
        Sensor::factory()->create();
    }

    /**
     * As a user with a house, I want to get all the sensors inside.
     *
     * @return void
     */
    public function test_get_sensors_of_house()
    {

        // Acting as a user with a house
        Sanctum::actingAs(User::find(1));

        $response = $this->getJson(route($this->url, ['id' => Home::find(1)->id]));

        $response->assertStatus(200);

        $sensor1 = Sensor::find(1);
        $sensor2 = Sensor::find(2);

        $response->assertJson([
            'data' => [
                0 => [
                    'id' => $sensor1->id,
                    'room' => $sensor1->room,
                    'home_id' => Home::find(1)->id
                ],
                1 => [
                    'id' => $sensor2->id,
                    'room' => $sensor2->room,
                    'home_id' => Home::find(1)->id
                ]
            ]
        ]);
    }

    /**
     * As a user who is not authenticated, I want to get all the sensors of a house. -> Requires authentication!
     *
     * @return void
     */
    public function test_user_is_not_authenticated()
    {

        // Acting as a user who is not authenticated

        $response = $this->getJson(route($this->url, ['id' => Home::find(1)->id]));

        $response->assertStatus(401);
    }


    /**
     * User is not the owner of the house
     *
     * @return void
     */
    public function test_user_is_not_owner_of_the_house()
    {

        // Acting as a user without a house
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson(route($this->url, ['id' => Home::find(1)->id]));

        $response->assertStatus(403);
    }


    /**
     * House could not be found
     *
     * @return void
     */
    public function test_house_could_not_be_found()
    {

        // Acting as a user with a house
        Sanctum::actingAs(User::find(1));

        $response = $this->getJson(route($this->url, ['id' => 324234]));

        $response->assertStatus(404);
    }
}
