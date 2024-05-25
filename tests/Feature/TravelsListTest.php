<?php

namespace Tests\Feature;

use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TravelsListTest extends TestCase
{
    use RefreshDatabase; // to refresh the testing database each time the test is called.

    public function test_travels_list_returns_paginated_data_correctly(): void 
    {
        Travel::factory(16)->create(['is_public' => true]); // creating 16 public records because the pagination should return only 15 records and we will test that.

        $response = $this->get('/api/v1/travels'); // navigating to the travels route.

        $response->assertStatus(200); // first assert that we must get status of 200 and that means we getting data from this route.
        $response->assertJsonCount(15, 'data'); // secondly we assert that the records count of the 'data' key of the collection is 15 by the pagination and not 16.
        $response->assertJsonPath('meta.last_page', 2); // the third assert we be testing the path of the last page of pagination coming from the meta key is 2 because we have only to pages with this test.
    }
}
