<?php

namespace Tests\Feature\Home;

use App\Models\Home;
use App\Models\Prediction;
use App\Models\Sensor;
use App\Models\Temperature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteHomeTest extends TestCase
{
    use RefreshDatabase;

    protected $url = 'api.homes.destroy';

    public function setUp(): void
    {
        parent::setUp();

        $home = Home::factory()->create();

        $sensor = Sensor::factory()->create([
            'home_id' => $home->id,
        ]);

        $temperature = Temperature::factory()->create([
            'sensor_id' => $sensor->id
        ]);
        Prediction::factory()->create([
            'temperature_id' => $temperature->id,
            'sensor_id' => $sensor->id
        ]);


    }

    /**
     * As a user I want to delete a house I own
     *
     * @return void
     */
    public function test_delete_a_house()
    {
        // Acting as an authenticated user
        $user = User::find(1);
        Sanctum::actingAs($user);

        $this->assertDatabaseCount('homes', 1);
        $this->assertDatabaseCount('sensors', 1);
        $this->assertDatabaseCount('temperatures', 1);
        $this->assertDatabaseCount('predictions', 1);
        $this->assertDatabaseCount('users', 1);


        $response = $this->deleteJson(route($this->url, ['id' => Home::find(1)->id]));

        $response->assertStatus(204);

        $this->assertDatabaseCount('homes', 0);
        $this->assertDatabaseCount('sensors', 0);
        $this->assertDatabaseCount('temperatures', 0);
        $this->assertDatabaseCount('predictions', 0);
        $this->assertDatabaseCount('users', 1);
    }

    /**
     * As a user I want to delete a house I DON'T own -> Forbidden
     *
     * @return void
     */
    public function test_delete_a_house_i_dont_own()
    {
        // Acting as an authenticated user
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->assertDatabaseCount('homes', 1);
        $this->assertDatabaseCount('sensors', 1);
        $this->assertDatabaseCount('temperatures', 1);
        $this->assertDatabaseCount('predictions', 1);
        $this->assertDatabaseCount('users', 2);


        $response = $this->deleteJson(route($this->url, ['id' => Home::find(1)->id]));

        $response->assertStatus(403);

        $this->assertDatabaseCount('homes', 1);
        $this->assertDatabaseCount('sensors', 1);
        $this->assertDatabaseCount('temperatures', 1);
        $this->assertDatabaseCount('predictions', 1);
        $this->assertDatabaseCount('users', 2);
    }

    /**
     * As a user who is not authenticated I want to delete a house I own. -> Requires authentication!
     *
     * @return void
     */
    public function test_user_is_not_authenticated()
    {
        // Acting as an non-authenticated user


        $this->assertDatabaseCount('homes', 1);
        $this->assertDatabaseCount('sensors', 1);
        $this->assertDatabaseCount('temperatures', 1);
        $this->assertDatabaseCount('predictions', 1);
        $this->assertDatabaseCount('users', 1);


        $response = $this->deleteJson(route($this->url, ['id' => Home::find(1)->id]));

        $response->assertStatus(401);

        $this->assertDatabaseCount('homes', 1);
        $this->assertDatabaseCount('sensors', 1);
        $this->assertDatabaseCount('temperatures', 1);
        $this->assertDatabaseCount('predictions', 1);
        $this->assertDatabaseCount('users', 1);
    }


    /**
     * As a user who authenticated I want to delete a house that does not exist. -> Not found!
     *
     * @return void
     */
    public function test_delete_a_house_that_does_not_exist()
    {
        // Acting as an authenticated user
        $user = User::find(1);
        Sanctum::actingAs($user);

        $this->assertDatabaseCount('homes', 1);
        $this->assertDatabaseCount('sensors', 1);
        $this->assertDatabaseCount('temperatures', 1);
        $this->assertDatabaseCount('predictions', 1);
        $this->assertDatabaseCount('users', 1);


        $response = $this->deleteJson(route($this->url, ['id' => 34023074230874823]));

        $response->assertStatus(404);

        $this->assertDatabaseCount('homes', 1);
        $this->assertDatabaseCount('sensors', 1);
        $this->assertDatabaseCount('temperatures', 1);
        $this->assertDatabaseCount('predictions', 1);
        $this->assertDatabaseCount('users', 1);
    }


}
