<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;
    protected $url = 'api.users.register';



    /**
     * Register a new user
     *
     * @return void
     */
    public function test_register_successfully()
    {
        $requestBody = [
            'username' => fake()->name,
            'password' => '1234'
        ];

        $response = $this->postJson(route($this->url), $requestBody);

        $response->assertStatus(201);

        $this->assertDatabaseCount('users', 1);

        $response->assertJsonStructure([
            'data' => [
                'token'
            ]
        ]);

        // Test the user can log in
        $this->postJson(route('api.users.login'), $requestBody)
            ->assertStatus(200);


    }


    /**
     * Username already taken
     *
     * @return void
     */
    public function test_username_already_taken()
    {

        User::factory()->create([
            'username' => $username = 'Robert'
        ]);

        $requestBody = [
            'username' => $username,
            'password' => '1234'
        ];

        $response = $this->postJson(route($this->url), $requestBody);

        $response->assertStatus(422);

        $this->assertDatabaseCount('users', 1);

        $response->assertJsonStructure([
            'message',
            'errors' => [
                'username'
            ]
        ]);
    }

    /**
     * Missing values
     *
     * @return void
     */
    public function test_missing_values()
    {


        $requestBody = [

        ];

        $response = $this->postJson(route($this->url), $requestBody);

        $response->assertStatus(422);

        $this->assertDatabaseCount('users', 0);

        $response->assertJsonStructure([
            'message',
            'errors' => [
                'username',
                'password'
            ]
        ]);
    }

    /**
     * Too long values
     *
     * @return void
     */
    public function test_too_long_values()
    {


        $requestBody = [
            'username' => str_repeat('a', 102),
            'password' => str_repeat('b', 2000)

        ];

        $response = $this->postJson(route($this->url), $requestBody);

        $response->assertStatus(422);

        $this->assertDatabaseCount('users', 0);

        $response->assertJsonStructure([
            'message',
            'errors' => [
                'username',
                'password'
            ]
        ]);
    }

    /**
     * Short password
     *
     * @return void
     */
    public function test_short_password()
    {


        $requestBody = [
            'username' => fake()->name,
            'password' => 's'

        ];

        $response = $this->postJson(route($this->url), $requestBody);

        $response->assertStatus(422);

        $this->assertDatabaseCount('users', 0);

        $response->assertJsonStructure([
            'message',
            'errors' => [
                'password'
            ]
        ]);
    }
}
