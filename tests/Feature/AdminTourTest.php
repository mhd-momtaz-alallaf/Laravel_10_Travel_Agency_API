<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
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

    public function test_non_admin_user_cannot_access_adding_tour_route(): void
    {
        $this->seed(RoleSeeder::class); // to seed the roles into the testing database.

        $user = User::factory()->create(); // create a new user.

        $user->roles()->attach(Role::where('name' ,'editor')->value('id')); // attach the editor role id to the created user.
        
        $travel  = Travel::factory()->create(); // create a travel.

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->id.'/tours'); // navigate to the create travel tours route by the user who having the editor role.

        $response->assertStatus(403); // assert getting forbidden (unauthorized) code.
    }
}
