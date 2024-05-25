<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TourListTest extends TestCase
{
    use RefreshDatabase;

    public function test_tour_list_by_travel_slug_returns_correct_tours(): void
    {

        $travel = Travel::factory()->create(); // create 1 travel.

        $tour = Tour::factory()->create(['travel_id' => $travel->id]); // create 1 tour associated with above travel.

        $response = $this->get('/api/v1/travels/'. $travel->slug .'/tours'); // navigate to the tour route.

        $response->assertStatus(200); // assert getting data from the route (the route is exist and correct).
        $response->assertJsonCount(1, 'data'); // assert have only one record.
        $response->assertJsonFragment(['id'=> $tour->id]); // to see if the id of that tour is exist.
    }

    public function test_tour_price_is_shown_correctly(): void
    {

        $travel = Travel::factory()->create(); // create 1 travel.

        $tour = Tour::factory()->create([ // create 1 tour associated with above travel.
            'travel_id' => $travel->id,
            'price' => 123.45,
        ]);

        $response = $this->get('/api/v1/travels/'. $travel->slug .'/tours'); // navigate to the tour route.

        $response->assertStatus(200); // assert getting data from the route (the route is exist and correct).
        $response->assertJsonCount(1, 'data'); // assert have only one record.
        $response->assertJsonFragment(['price'=> 123.45]); // to see if the price of that tour is correct.
    }
}
