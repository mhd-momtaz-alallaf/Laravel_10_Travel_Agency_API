<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_with_valid_credentials(): void
    {
        $user = User::factory()->create(); // create new user.

        $response = $this->postJson('/api/v1/login',[ // navigate to the login route, passing valid email and password.
            'email'=> $user->email,
            'password'=> 'password',
        ]);

        $response->assertStatus(200); // assert that the response is ok.
        $response->assertJsonStructure(['access_token']); // assert the response returns the access_token.
    }
}
