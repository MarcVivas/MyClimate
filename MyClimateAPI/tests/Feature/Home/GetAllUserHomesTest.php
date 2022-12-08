<?php

namespace Tests\Feature\Home;

use App\Models\Home;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetAllUserHomesTest extends TestCase
{
    use RefreshDatabase;

    protected $url = 'api.homes.getUserHomes';

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();

        Home::factory()->create([
            'address' => 'Avocado street 103',
            'description' => 'House with barbecue!',
            'user_id' => $user->id
        ]);

        Home::factory()->create([
            'address' => 'Avocado street 70',
            'description' => 'House with garden!',
            'user_id' => $user->id
        ]);
    }

    /**
     * Acting as a user who is not authenticated
     * @return void
     */
    public function test_user_is_not_authenticated(){
        $this->getJson(route($this->url))
            ->assertStatus(401);

    }

    /**
     * Get all homes of a user
     *
     * @return void
     */
    public function test_get_all_homes()
    {

        Sanctum::actingAs(User::find(1));

        $response = $this->getJson(route($this->url));

        $response->assertStatus(200);

        $home1 = Home::find(1);
        $home2 = Home::find(2);

        $response->assertJson([
            'data' => [
                0 => [
                    'id' => $home1->id,
                    'address' => $home1->address,
                    'description' => $home1->description,
                    'name' => $home1->name,
                    'user_id' => User::find(1)->id

                ],
                1 => [
                    'id' => $home2->id,
                    'address' => $home2->address,
                    'description' => $home2->description,
                    'name' => $home2->name,
                    'user_id' => User::find(1)->id
                ]
            ]
        ]);
    }
}
