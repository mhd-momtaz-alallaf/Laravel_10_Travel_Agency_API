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

    public function test_admin_creates_tour_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class); // to seed the roles into the testing database.

        $user = User::factory()->create(); // create new user.

        $user->roles()->attach(Role::where('name' ,'admin')->value('id')); // attach the admin role id to the created user.
        
        $travel  = Travel::factory()->create(); // create a travel.

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->id.'/tours',[ // login into the system as the created $user (the admin) and navigate to admin tours route.
            'name'=> 'Tour Name', //Case 1: passing only the tour name, skipping the rest of the required fields.
        ]); 

        $response->assertStatus(422); // assert getting validation error (422) code.
        //----------------------------------------------------------------------------
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->id.'/tours',[ // login into the system as the created $user (the admin) and navigate to admin tours route.
            'name'=> 'Tour Name', //Case 2: passing All required fields.
            'starting_date' => now()->toDateString(),
            'ending_date' => now()->addDay()->toDateString(),
            'price' => 123.45,
        ]); 

        $response->assertStatus(201); // assert getting created successfully (201) code.
        //----------------------------------------------------------------------------
        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours'); //Case 3: get the list of all the tours of a specific travel by the travel slug.

        $response->assertJsonFragment(['name' => 'Tour Name']); // assert that the name of the newly created tour is there in the tours list.
    }
}
