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

    public function test_tours_list_by_travel_slug_returns_correct_tours(): void
    {
        $travel = Travel::factory()->create(); // create 1 travel.

        $tour = Tour::factory()->create(['travel_id' => $travel->id]); // create 1 tour associated with above travel.

        $response = $this->get('/api/v1/travels/'. $travel->slug .'/tours'); // navigate to the tours route.

        $response->assertStatus(200); // assert getting data from the route (the route is exist and correct).
        $response->assertJsonCount(1, 'data'); // assert have only one record.
        $response->assertJsonFragment(['id'=> $tour->id]); // to see if the id of that tour is exist.
    }

    public function test_tours_price_is_shown_correctly(): void
    {
        $travel = Travel::factory()->create(); // create 1 travel.

        $tour = Tour::factory()->create([ // create 1 tour associated with above travel.
            'travel_id' => $travel->id,
            'price' => 123.45,
        ]);

        $response = $this->get('/api/v1/travels/'. $travel->slug .'/tours'); // navigate to the tours route.

        $response->assertStatus(200); // assert getting data from the route (the route is exist and correct).
        $response->assertJsonCount(1, 'data'); // assert have only one record.
        $response->assertJsonFragment(['price'=> '123.45']); // to see if the price of that tour is correct.
    }

    public function test_tours_list_returns_pagination(): void
    {
        $travel = Travel::factory()->create(); // create 1 travel.

        $tour = Tour::factory(16)->create(['travel_id' => $travel->id]); // create 16 tour associated with above travel.

        $response = $this->get('/api/v1/travels/'. $travel->slug .'/tours'); // navigate to the tours route.

        $response->assertStatus(200); // assert getting data from the route (the route is exist and correct).
        $response->assertJsonCount(15, 'data'); // assert having 15 records in the 'data' key by the pagination.
        $response->assertJsonPath('meta.last_page', 2); // assert the path of the last page of pagination coming from the meta key is 2 because we have only tow pages for this tour.
    }

    public function test_tours_list_sorts_by_starting_date_correctly(): void
    {
        $travel = Travel::factory()->create(); // create 1 travel.

        $laterTour = Tour::factory()->create([ // create 1 tour have starting date is later because it starts after tow days from now.
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);

        $earlierTour = Tour::factory()->create([ // create 1 tour have starting date is earlier because it starts now.
            'travel_id' => $travel->id,
            'starting_date' => now(),
            'ending_date' => now()->addDays(1),
        ]);

        $response = $this->get('/api/v1/travels/'. $travel->slug .'/tours'); // navigate to the tours route.

        $response->assertStatus(200); // assert getting data from the route (the route is exist and correct).
        $response->assertJsonPath('data.0.id', $earlierTour->id); // assert that the earlierTour(that have index 0) will appears first and before the laterTour(that have index 1)
        $response->assertJsonPath('data.1.id', $laterTour->id);  // assert that the laterTour(that have index 1) will appears in the last and after the earlierTour(that have index 0) 
    }
}
