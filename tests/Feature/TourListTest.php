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

    public function test_tours_list_sorts_by_price_then_by_date_correctly(): void
    {
        $travel = Travel::factory()->create(); // create 1 travel.

        $expensiveTour = Tour::factory()->create([ // create 1 tour have expensive price = 200.
            'travel_id' => $travel->id,
            'price' => 200,
        ]);

        $cheapLaterTour = Tour::factory()->create([ // create 1 tour have cheap price and Later starting_date.
            'travel_id' => $travel->id,
            'price'=> 100,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);

        $cheapEarlierTour = Tour::factory()->create([ // create 1 tour have cheap price and early starting_date.
            'travel_id' => $travel->id,
            'price'=> 100,
            'starting_date' => now(),
            'ending_date' => now()->addDays(1),
        ]);

        $response = $this->get('/api/v1/travels/'. $travel->slug .'/tours?sortBy=price&sortOrder=asc'); // navigate to the tours route with sortBy and sortOrder parameters.

        $response->assertStatus(200); // assert getting data from the route (the route is exist and correct).

        // The sortBy is 'price' and the sortOrder is 'asc' then we have the fix orderBy that is 'starting_date', so the results must be:
        $response->assertJsonPath('data.0.id', $cheapEarlierTour->id); // first cheaper price and the earlier date.
        $response->assertJsonPath('data.1.id', $cheapLaterTour->id);  // secondly the cheaper but have later date.  
        $response->assertJsonPath('data.2.id', $expensiveTour->id);  // in the last the most expensive in price.
    }

    public function test_tours_list_filters_by_price_correctly(): void
    {
        $travel = Travel::factory()->create(); // create 1 travel.

        $expensiveTour = Tour::factory()->create([ // create 1 tour have expensive price = 200.
            'travel_id' => $travel->id,
            'price' => 200,
        ]);

        $cheapTour = Tour::factory()->create([ // create 1 tour have cheap price = 100.
            'travel_id' => $travel->id,
            'price'=> 100,
        ]);

        $endpoint = '/api/v1/travels/'. $travel->slug .'/tours'; // create a general endpoint to use it in a deferent cases.

        $response = $this->get($endpoint .'?priceFrom=100'); // case 1

        $response->assertJsonCount(2, 'data'); // assert we have 2 records.
        $response->assertJsonFragment(['id'=> $cheapTour->id]);  // assert the cheap tour is there because its have the price 100 and its within the filter.  
        $response->assertJsonFragment(['id'=> $expensiveTour->id]);  // assert the expensive tour there because its have the price 200 and its within the filter.
        //-----------------------------------------------------------------------------
        $response = $this->get($endpoint .'?priceFrom=150'); // case 2

        $response->assertJsonCount(1, 'data'); // assert we have only 1 record.
        $response->assertJsonMissing(['id'=> $cheapTour->id]);  // assert the cheap tour is Not there because its have the price 100 and its out of the filter range.  
        $response->assertJsonFragment(['id'=> $expensiveTour->id]);  // assert the expensive tour there because its have the price 200 and its within the filter.
        //-----------------------------------------------------------------------------
        $response = $this->get($endpoint .'?priceFrom=250'); // case 3

        $response->assertJsonCount(0, 'data'); // assert we have No records at all because its out of the filter range.
        //-----------------------------------------------------------------------------
        $response = $this->get($endpoint .'?priceTo=200'); // case 4

        $response->assertJsonCount(2, 'data'); // assert we have 2 records.
        $response->assertJsonFragment(['id'=> $cheapTour->id]);  // assert the cheap tour is there because its have the price 100 and its within the filter.  
        $response->assertJsonFragment(['id'=> $expensiveTour->id]);  // assert the expensive tour there because its have the price 200 and its within the filter.
        //-----------------------------------------------------------------------------
        $response = $this->get($endpoint .'?priceTo=150'); // case 5

        $response->assertJsonCount(1, 'data'); // assert we have only 1 record.
        $response->assertJsonFragment(['id'=> $cheapTour->id]);  // assert the cheap tour is there because its have the price 100 and its within the filter.  
        $response->assertJsonMissing(['id'=> $expensiveTour->id]);  // assert the expensive tour is Not there because its have the price 200 and its out of the filter range.
        //-----------------------------------------------------------------------------
        $response = $this->get($endpoint .'?priceTo=50'); // case 6

        $response->assertJsonCount(0, 'data'); // assert we have No records at all because its out of the filter range.
        //-----------------------------------------------------------------------------
        $response = $this->get($endpoint .'?priceFrom=150&priceTo=250'); // case 7

        $response->assertJsonCount(1, 'data'); // assert we have only 1 record.
        $response->assertJsonMissing(['id'=> $cheapTour->id]);  // assert the cheap tour is Not there because its have the price 100 and its out of the filter range.  
        $response->assertJsonFragment(['id'=> $expensiveTour->id]);  // assert the expensive tour there because its have the price 200 and its within the filter.
    }

    public function test_tours_list_filters_by_starting_date_correctly(): void
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

        $endpoint = '/api/v1/travels/'. $travel->slug .'/tours'; // create a general endpoint to use it in a deferent cases.

        $response = $this->get($endpoint .'?dateFrom='.now()); // case 1

        $response->assertJsonCount(2, 'data'); // assert we have 2 records.
        $response->assertJsonFragment(['id'=> $earlierTour->id]);  // assert the earlier tour is there because its have the starting_date from now() and its within the filter.  
        $response->assertJsonFragment(['id'=> $laterTour->id]);  // assert the later tour there because its have the starting_date from now(+2 days) and its within the filter.
        //-----------------------------------------------------------------------------
        $response = $this->get($endpoint .'?dateFrom='.now()->addDay()); // case 2

        $response->assertJsonCount(1, 'data'); // assert we have only 1 record.
        $response->assertJsonMissing(['id'=> $earlierTour->id]);  // assert the earlier tour is Not there because its have the starting_date from now() and its out of the filter range.  
        $response->assertJsonFragment(['id'=> $laterTour->id]);  // assert the later tour there because its have the starting_date from now(+2 days) and its within the filter.
        //-----------------------------------------------------------------------------
        $response = $this->get($endpoint .'?dateFrom='.now()->addDays(5)); // case 3

        $response->assertJsonCount(0, 'data'); // assert we have No records at all because its out of the filter range.
        //-----------------------------------------------------------------------------
        $response = $this->get($endpoint .'?dateTo='.now()->addDays(5)); // case 4

        $response->assertJsonCount(2, 'data'); // assert we have 2 records.
        $response->assertJsonFragment(['id'=> $earlierTour->id]);  // assert the earlier tour is there because its have the starting_date from now() and its within the filter.  
        $response->assertJsonFragment(['id'=> $laterTour->id]);  // assert the later tour there because its have the starting_date from now(+2 days) and its within the filter.
        //-----------------------------------------------------------------------------
        $response = $this->get($endpoint .'?dateTo='.now()->addDay()); // case 5

        $response->assertJsonCount(1, 'data'); // assert we have only 1 record.
        $response->assertJsonFragment(['id'=> $earlierTour->id]);  // assert the earlier tour is there because its have the starting_date from now() and its within the filter.  
        $response->assertJsonMissing(['id'=> $laterTour->id]);  // assert the later tour is Not there because its have the starting_date from now(+2 days) and its out of the filter range.
        //-----------------------------------------------------------------------------
        $response = $this->get($endpoint .'?dateTo='.now()->subDay()); // case 6

        $response->assertJsonCount(0, 'data'); // assert we have No records at all because its out of the filter range.
        //-----------------------------------------------------------------------------
        $response = $this->get($endpoint .'?dateFrom='.now()->addDay().'&dateTo='.now()->addDays(5)); // case 7

        $response->assertJsonCount(1, 'data'); // assert we have only 1 record.
        $response->assertJsonMissing(['id'=> $earlierTour->id]);  // assert the earlier tour is Not there because its have the starting_date from now() and its out of the filter range.  
        $response->assertJsonFragment(['id'=> $laterTour->id]);  // assert the later tour there because its have the starting_date from now(+2 days) and its within the filter.
    }

    public function test_tours_list_returns_validation_errors(): void
    {
        $travel = Travel::factory()->create(); // create 1 travel.

        $response = $this->getJson('/api/v1/travels/'. $travel->slug .'/tours?dateFrom=wrongDateFormat'); // navigate to tours route with date filter have a wrong date format.
        $response->assertStatus(422); // assert having validation error response (422).

        $response = $this->getJson('/api/v1/travels/'. $travel->slug .'/tours?priceFrom=wrongPriceFormat'); // navigate to tours route with price filter have a wrong price format(string).
        $response->assertStatus(422); // assert having validation error response (422).
    }
}
