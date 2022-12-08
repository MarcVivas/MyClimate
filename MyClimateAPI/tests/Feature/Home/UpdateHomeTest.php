<?php

namespace Tests\Feature\Home;

use App\Models\Home;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateHomeTest extends TestCase
{

    use RefreshDatabase;
    protected $url = 'api.homes.update';

    public function setUp(): void
    {
        parent::setUp();

        Home::factory()->create();
    }

    /**
     * As a user I want to update my own home
     *
     * @return void
     */
    public function test_update_a_home_successfully()
    {
        // Acting as an authenticated user
        Sanctum::actingAs(User::find(1));

        $requestBody = [
            'name' => fake()->name,
            'address' => fake()->streetAddress,
            'description' => fake()->text
        ];

        $response = $this->patchJson(route($this->url, ['id' => 1]), $requestBody);

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'name' => $requestBody['name'],
                'address' => $requestBody['address'],
                'description' =>  $requestBody['description']
            ]
        ]);

        $home = Home::find(1);
        self::assertEquals($requestBody['description'], $home->description);
        self::assertEquals($requestBody['address'], $home->address);
        self::assertEquals($requestBody['name'], $home->name);

        // Change only the name
        $response = $this->patchJson(route($this->url, ['id' => $home->id]), ['name' => 'hello']);

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'name' => 'hello',
                'address' => $requestBody['address'],
                'description' =>  $requestBody['description']
            ]
        ]);

        $home = Home::find(1);
        self::assertEquals($requestBody['description'], $home->description);
        self::assertEquals($requestBody['address'], $home->address);
        self::assertEquals('hello', $home->name);

    }


    /**
     * Empty strings delete the text
     *
     * @return void
     */
    public function test_empty_strings_delete_the_current_text()
    {
        // Acting as an authenticated user
        Sanctum::actingAs(User::find(1));

        $requestBody = [
            'name' => "",
            'address' => "",
            'description' => ""
        ];

        $home = Home::find(1);

        $response = $this->patchJson(route($this->url, ['id' => $home->id]), $requestBody);

        $response->assertStatus(200);

        $home = Home::find(1);

        $response->assertJson([
            'data' =>  [
                'name' => $requestBody['name'],
                'address' => $requestBody['address'],
                'description' =>  $requestBody['description']
            ]
        ]);

        self::assertEquals($requestBody['description'], $home->description);
        self::assertEquals($requestBody['address'], $home->address);
        self::assertEquals($requestBody['name'], $home->name);
    }


    /**
     * too long inputs
     *
     * @return void
     */
    public function test_too_long_values()
    {
        // Acting as an authenticated user
        Sanctum::actingAs(User::find(1));

        $requestBody = [
            'name' => str_repeat('a', 400),
            'address' => str_repeat('a', 4000),
            'description' => str_repeat('a', 4000)
        ];

        $home = Home::find(1);

        $previousHome = [
            'name' => $home->name,
            'address' => $home->address,
            'description' => $home->description
        ];

        $response = $this->patchJson(route($this->url, ['id' => $home->id]), $requestBody);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors' => [
                'name',
                'address',
                'description'
            ],
            'message'
        ]);

        $home = Home::find(1);

        self::assertEquals($previousHome['description'], $home->description);
        self::assertEquals($previousHome['address'], $home->address);
        self::assertEquals($previousHome['name'], $home->name);
    }


    /**
     * As a user I want to update another house that I am not the owner of.
     *
     * @return void
     */
    public function test_user_is_not_the_owner_of_the_house()
    {
        // Acting as an authenticated user
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $requestBody = [
            'name' => fake()->name,
            'address' => fake()->streetAddress,
            'description' => fake()->text
        ];

        $home = Home::find(1);

        $previousHome = [
            'name' => $home->name,
            'address' => $home->address,
            'description' => $home->description
        ];

        $response = $this->patchJson(route($this->url, ['id' => $home->id]), $requestBody);

        $response->assertStatus(403);

        $home = Home::find(1);

        self::assertEquals($previousHome['description'], $home->description);
        self::assertEquals($previousHome['address'], $home->address);
        self::assertEquals($previousHome['name'], $home->name);
    }


    /**
     * As a user I want to update my own house but I'm not authenticated.
     *
     * @return void
     */
    public function test_user_is_not_authenticated()
    {
        // Acting as an non-authenticated user

        $requestBody = [
            'name' => fake()->name,
            'address' => fake()->streetAddress,
            'description' => fake()->text
        ];

        $home = Home::find(1);

        $previousHome = [
            'name' => $home->name,
            'address' => $home->address,
            'description' => $home->description
        ];

        $response = $this->patchJson(route($this->url, ['id' => $home->id]), $requestBody);

        $response->assertStatus(401);

        $home = Home::find(1);

        self::assertEquals($previousHome['description'], $home->description);
        self::assertEquals($previousHome['address'], $home->address);
        self::assertEquals($previousHome['name'], $home->name);
    }

}
