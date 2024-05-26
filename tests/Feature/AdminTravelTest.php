<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTravelTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_unauthenticated_user_cannot_access_adding_travel_route(): void
    {
        $response = $this->postJson('/api/v1/admin/travels'); // navigate to the create travels route without login.

        $response->assertStatus(401); // assert getting unauthenticated code.
    }

    public function test_non_admin_user_cannot_access_adding_travel_route(): void
    {
        $this->seed(RoleSeeder::class); // to seed the roles into the testing database.

        $user = User::factory()->create(); // create new user.

        $user->roles()->attach(Role::where('name' ,'editor')->value('id')); // attach the editor role id to the created user.
        
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels'); // login to the system as the created $user and navigate to admin travels route

        $response->assertStatus(403); // assert getting forbidden (unauthorized) code.
    }
}
