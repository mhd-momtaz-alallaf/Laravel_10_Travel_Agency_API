<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelRequest;
use App\Http\Requests\UpdateTravelRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;

class TravelController extends Controller
{
    public function store(TravelRequest $request) // this method is for creating a new travel by the admin.
    {
        $travel = Travel::create($request->validated());

        return new TravelResource($travel);
    }

    public function update(Travel $travel, UpdateTravelRequest $request) // this method is for Editing an existing travel by the admin or the editor (RoleMiddleware will be applied).
    {
        $travel->update($request->validated());

        return new TravelResource($travel);
    }
}
