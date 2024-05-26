<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTravelTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_unauthenticated_user_cannot_access_adding_travel_route(): void
    {
        $response = $this->postJson('/api/v1/admin/travels'); // navigate to the create travels route without login.

        $response->assertStatus(401); // assert getting unauthenticated code.
    }
}
