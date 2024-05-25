<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;
use App\Models\Travel;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Travel $travel, Request $request) // the route model binding is travel because the tour is scoped by the travel (there is not tour without travel).
    {
        $tours = $travel->tours()
            ->when($request->priceFrom, function ($query) use ($request) { // to filter by tour price, via the priceFrom optional route parameter.
                $query->where('price', '>=', $request->priceFrom * 100); // priceFrom * 100 to have the right Matching with the database price because the price in the database is * 100.
            })
            ->when($request->priceTo, function ($query) use ($request) { // to filter by priceTo optional route parameter.
                $query->where('price', '<=', $request->priceTo * 100);
            })
            ->when($request->dateFrom, function ($query) use ($request) { // to filter by dateFrom optional route parameter.
                $query->where('starting_date', '>=', $request->dateFrom);
            })
            ->when($request->dateTo, function ($query) use ($request) { // to filter by dateTo route optional parameter.
                $query->where('starting_date', '<=', $request->dateTo);
            })
            ->orderBy('starting_date')
            ->paginate();

        return TourResource::collection($tours);
    }
}