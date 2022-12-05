<?php

namespace Tests\Feature\Home;

use App\Models\Home;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetSpecificHouseTest extends TestCase
{
    use RefreshDatabase;

    protected $url = 'api.homes.show';

    public function setUp(): void
    {
        parent::setUp();

        Home::factory()->create();
    }

    /**
     * A user who is authenticated gets a home
     *
     * @return void
     */
    public function test_get_a_home()
    {

        // Acting as an authenticated user
        Sanctum::actingAs(User::find(1));

        $response = $this->getJson(route($this->url, ['id' => 1]));
        $response->assertStatus(200);

        $home = Home::find(1);

        $response->assertJson([
            'data' => [
                'id' => $home->id,
                'user_id' => $home->user_id,
                'address' => $home->address,
                'description' => $home->description,
                'name' => $home->name
            ]
        ]);
    }

    /**
     * A user who is authenticated wants to get a home that does not exist
     *
     * @return void
     */
    public function test_home_not_found()
    {
        // Acting as an authenticated user
        Sanctum::actingAs(User::find(1));

        $response = $this->getJson(route($this->url, ['id' => 324231]));
        $response->assertStatus(404);
    }

    /**
     * A user who is not authenticated tries to get a home
     *
     * @return void
     */
    public function test_user_is_not_authenticated()
    {
        $response = $this->getJson(route($this->url, ['id' => 1]));
        $response->assertStatus(401);
    }
}
