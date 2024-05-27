<?php

namespace Tests\Feature;

use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTourTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_unauthenticated_user_cannot_access_adding_tour_route(): void
    {
        $travel  = Travel::factory()->create(); // create a travel.

        $response = $this->postJson('/api/v1/admin/travels/'.$travel->id.'/tours'); // navigate to the create travel tours route without login.

        $response->assertStatus(401); // assert getting unauthenticated code.
    }
}
