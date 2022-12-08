<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * TEST POST /auth/login
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected $url = 'api.users.login';

    public function setUp(): void
    {
        parent::setUp();

        User::factory()->create(
            [
                'password' => Hash::make('12345'),
            ]
        );
    }

    /**
     * As a user who has already registered I want to login
     *
     * @return void
     */
    public function test_login_successfully()
    {

        $requestBody = [
            'username' => User::find(1)->username,
            'password' => '12345'
        ];

        $response = $this->postJson(route('api.users.login'), $requestBody);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' =>  [
                'token'
            ]
        ]);

        // Check if the user is authenticated
        $this->postJson(
            uri: route('api.homes.store'),
            data: ['name' => 'name', 'address' => 'address', 'description' => 'des'],
            headers: ['Authorization' => 'Bearer '. $response->json("data.token")]
        )
            ->assertStatus(201);

    }

    /**
     * User not found, there is no user with the given username
     *
     * @return void
     */
    public function test_user_not_found()
    {

        $requestBody = [
            'username' => 'notFound',
            'password' => '12345'
        ];

        $response = $this->postJson(route('api.users.login'), $requestBody);

        // nOt found
        $response->assertStatus(404);
    }


    /**
     * Password does not match
     *
     * @return void
     */
    public function test_password_does_not_match()
    {

        $requestBody = [
            'username' => User::find(1)->username,
            'password' => '-----'
        ];

        $response = $this->postJson(route('api.users.login'), $requestBody);

        // Forbidden
        $response->assertStatus(403);
    }

    /**
     * Too long inputs
     *
     * @return void
     */
    public function test_too_long_inputs()
    {

        $requestBody = [
            'username' => str_repeat('a', 200),
            'password' => str_repeat('a', 2000),
        ];

        $response = $this->postJson(route('api.users.login'), $requestBody);

        // Forbidden
        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors' => [
                'username',
                'password'
            ],
            'message'
        ]);
    }

    /**
     * Inputs are not given
     *
     * @return void
     */
    public function test_inputs_are_not_given()
    {

        $requestBody = [];

        $response = $this->postJson(route('api.users.login'), $requestBody);

        // Forbidden
        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors' => [
                'username',
                'password'
            ],
            'message'
        ]);
    }
}
