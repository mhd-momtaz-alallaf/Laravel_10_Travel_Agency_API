<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function store(Travel $travel, TourRequest $request) // this method is for adding a new tour into a specific travel.
    {
        $tour = $travel->tours()->create($request->validated());

        return new TourResource($tour);
    }
}
