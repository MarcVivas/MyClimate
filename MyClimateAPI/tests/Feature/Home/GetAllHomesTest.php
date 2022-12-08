<?php

namespace Tests\Feature\Home;

use App\Models\Home;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetAllHomesTest extends TestCase
{
    use RefreshDatabase;

    protected $url = 'api.homes.index';

    public function setUp(): void
    {
        parent::setUp();

        Home::factory()->create([
            'address' => 'Avocado street 103',
            'description' => 'House with barbecue!'
        ]);

        Home::factory()->create([
            'address' => 'Avocado street 70',
            'description' => 'House with garden!'
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
     * Get all homes without query parameters
     *
     * @return void
     */
    public function test_get_all_homes_without_query_parameters()
    {
        Sanctum::actingAs(User::factory()->create());

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
                    'user_id' => $home1->user_id

                ],
                1 => [
                    'id' => $home2->id,
                    'address' => $home2->address,
                    'description' => $home2->description,
                    'name' => $home2->name,
                    'user_id' => $home2->user_id
                ]
            ]
        ]);
    }

    /**
     * Get home 1 using filters
     *
     * @return void
     */
    public function test_test_get_home_1_using_filters()
    {
        Sanctum::actingAs(User::factory()->create());

        $filters = [
            'description' => ' with b',
            'address' => ' street 1'
        ];

        $response = $this->getJson(route($this->url, $filters));

        $response->assertStatus(200);

        $home1 = Home::find(1);

        self::assertEquals(1, count($response->json("data")));

        $response->assertJson([
            'data' => [
                0 => [
                    'id' => $home1->id,
                    'address' => $home1->address,
                    'description' => $home1->description,
                    'name' => $home1->name,
                    'user_id' => $home1->user_id

                ],
            ]
        ]);

        $filters = [
            'id' => 1
        ];
        $response = $this->getJson(route($this->url, $filters));

        $response->assertStatus(200);

        $home1 = Home::find(1);

        self::assertEquals(1, count($response->json("data")));

        $response->assertJson([
            'data' => [
                0 => [
                    'id' => $home1->id,
                    'address' => $home1->address,
                    'description' => $home1->description,
                    'name' => $home1->name,
                    'user_id' => $home1->user_id

                ],
            ]
        ]);
    }

    /**
     * Get home 2 using filters
     *
     * @return void
     */
    public function test_test_get_home_2_using_filters()
    {
        Sanctum::actingAs(User::factory()->create());

        $filters = [
            'description' => ' with g',
            'address' => ' street 7'
        ];

        $response = $this->getJson(route($this->url, $filters));

        $response->assertStatus(200);

        $home2 = Home::find(2);

        self::assertEquals(1, count($response->json("data")));

        $response->assertJson([
            'data' => [
                0 => [
                    'id' => $home2->id,
                    'address' => $home2->address,
                    'description' => $home2->description,
                    'name' => $home2->name,
                    'user_id' => $home2->user_id

                ],
            ]
        ]);

        $filters = [
            'id' => 2
        ];
        $response = $this->getJson(route($this->url, $filters));

        $response->assertStatus(200);

        $home2 = Home::find(2);

        self::assertEquals(1, count($response->json("data")));

        $response->assertJson([
            'data' => [
                0 => [
                    'id' => $home2->id,
                    'address' => $home2->address,
                    'description' => $home2->description,
                    'name' => $home2->name,
                    'user_id' => $home2->user_id

                ],
            ]
        ]);
    }

    /**
     * Get all homes using filters
     *
     * @return void
     */
    public function test_get_all_homes_using_filters()
    {
        Sanctum::actingAs(User::factory()->create());

        $filters = [
            'description' => 'House with ',
            'address' => 'Avocado street '
        ];
        $response = $this->getJson(route($this->url, $filters));

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
                    'user_id' => $home1->user_id

                ],
                1 => [
                    'id' => $home2->id,
                    'address' => $home2->address,
                    'description' => $home2->description,
                    'name' => $home2->name,
                    'user_id' => $home2->user_id
                ]
            ]
        ]);
    }
}
