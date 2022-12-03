<?php

namespace Tests\Feature;

use App\Models\Home;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;


/**
 *  Test POST /homes
 */
class CreateHomeTest extends TestCase
{

    use RefreshDatabase;

    protected $url = 'api.homes.store';

    public function setUp(): void
    {
        parent::setUp();

    }

    /**
     * As a user who is not authenticated I want to create a new home without owner.
     *
     * @return void
     */
    public function test_create_a_home_without_owner()
    {
        $requestBody = $this->getValidRequestBody();

        $response = $this->postJson(route($this->url), $requestBody);

        $response->assertStatus(201);

        $this->assertDatabaseCount('homes', 1);

        $response->assertJson([
            'data' => [
                'id' => Home::find(1)->id,
                'name' => $requestBody['name'],
                'address' => $requestBody['address'],
                'description' => $requestBody['description'],
                'user_id' => null,  // No owner
            ]
        ]);

    }


    /**
     * As a user who is authenticated I want to create a new home whose owner will be me.
     *
     * @return void
     */
    public function test_create_a_home_authenticated()
    {
        // Acting as an authenticated user
        Sanctum::actingAs(User::factory()->create());

        $requestBody = $this->getValidRequestBody();

        $response = $this->postJson(route($this->url), $requestBody);

        $response->assertStatus(201);

        $this->assertDatabaseCount('homes', 1);

        $response->assertJson([
            'data' => [
                'id' => Home::find(1)->id,
                'name' => $requestBody['name'],
                'address' => $requestBody['address'],
                'description' => $requestBody['description'],
                'user_id' => User::find(1)->id
            ]
        ]);
    }


    /**
     * Sending a request with an empty body
     *
     * @return void
     */
    public function test_missing_values()
    {

        $requestBody = [];

        $response = $this->postJson(route($this->url), $requestBody);

        $response->assertStatus(422);

        $this->assertDatabaseCount('homes', 0);

        $response->assertJsonStructure([
            'message',
            'errors' => [
                'address',
                'name',
                'description'
            ]
        ]);
    }

    /**
     * Sending a request with strings that are too long
     *
     * @return void
     */
    public function test_too_long_strings()
    {

        $requestBody = [
            'name' => str_repeat('a', 100),
            'address' => str_repeat('b', 200),
            'description' => str_repeat('c', 501)
        ];

        $response = $this->postJson(route($this->url), $requestBody);

        $response->assertStatus(422);

        $this->assertDatabaseCount('homes', 0);

        $response->assertJsonStructure([
            'message',
            'errors' => [
                'address',
                'name',
                'description'
            ]
        ]);
    }


    private function getValidRequestBody(){
        return [
            'address' => fake()->streetAddress(),
            'description' => fake()->text(30),
            'name' => fake()->domainName()
        ];
    }
}
