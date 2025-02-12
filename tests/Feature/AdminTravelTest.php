<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
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

    public function test_admin_creates_travel_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class); // to seed the roles into the testing database.

        $user = User::factory()->create(); // create new user.

        $user->roles()->attach(Role::where('name' ,'admin')->value('id')); // attach the admin role id to the created user.
        
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels',[ // login into the system as the created $user (the admin) and navigate to admin travels route.
            'name'=> 'Travel Name', //Case 1: passing only the travel name, skipping the rest of the required fields.
        ]); 

        $response->assertStatus(422); // assert getting validation error (422) code.
        //----------------------------------------------------------------------------
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels',[ // login into the system as the created $user (the admin) and navigate to admin travels route.
            'name'=> 'Travel Name', //Case 2: passing All required fields.
            'is_public' => 1,
            'description' => 'travel description',
            'number_of_days' => 5,
        ]); 

        $response->assertStatus(201); // assert getting created successfully (201) code.
        //----------------------------------------------------------------------------
        $response = $this->get('/api/v1/travels'); //Case 3: get the list of all the travels.

        $response->assertJsonFragment(['name' => 'Travel Name']); // assert that the name of the newly created travel is there.
    }

    public function test_admin_updates_travel_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class); // to seed the roles into the testing database.

        $user = User::factory()->create(); // create new user.

        $user->roles()->attach(Role::where('name' ,'admin')->value('id')); // attach the admin role id to the created user.
      
        $travel = Travel::factory()->create(); // create new travel.

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/'.$travel->id,[ // login into the system as the created $user (the admin) and navigate to update travels route.
            'name'=> 'Travel Name', //Case 1: passing only the travel name, skipping the rest of the required fields.
        ]); 

        $response->assertStatus(422); // assert getting validation error (422) code.
        //----------------------------------------------------------------------------
        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/'.$travel->id,[ // login into the system as the created $user (the admin) and navigate to update travels route.
            'name'=> 'Updated Travel Name', //Case 2: passing All required fields.
            'is_public' => 1,
            'description' => 'updated description',
            'number_of_days' => 5,
        ]); 

        $response->assertStatus(200); // assert getting Ok 200 code.
        //----------------------------------------------------------------------------
        $response = $this->get('/api/v1/travels'); //Case 3: get the list of all the travels.

        $response->assertJsonFragment(['name' => 'Updated Travel Name']); // assert that the name of the newly created travel is there.
    }

    public function test_editor_updates_travel_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class); // to seed the roles into the testing database.

        $user = User::factory()->create(); // create new user.

        $user->roles()->attach(Role::where('name' ,'editor')->value('id')); // attach the editor role id to the created user.
      
        $travel = Travel::factory()->create(); // create new travel.

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/'.$travel->id,[ // login into the system as the created $user (the editor) and navigate to update travels route.
            'name'=> 'Travel Name', //Case 1: passing only the travel name, skipping the rest of the required fields.
        ]); 

        $response->assertStatus(422); // assert getting validation error (422) code.
        //----------------------------------------------------------------------------
        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/'.$travel->id,[ // login into the system as the created $user (the editor) and navigate to update travels route.
            'name'=> 'Updated Travel Name', //Case 2: passing All required fields.
            'is_public' => 1,
            'description' => 'updated description',
            'number_of_days' => 5,
        ]); 

        $response->assertStatus(200); // assert getting Ok 200 code.
        //----------------------------------------------------------------------------
        $response = $this->get('/api/v1/travels'); //Case 3: get the list of all the travels.

        $response->assertJsonFragment(['name' => 'Updated Travel Name']); // assert that the name of the newly created travel is there.
    }
}
