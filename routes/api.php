<?php

use App\Http\Controllers\Api\V1\Admin\TravelController as AdminTravelController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\TravelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/travels',[TravelController::class, 'index']); // to get the list of public travels.
Route::get('/travels/{travel:slug}/tours',[TourController::class,'index']); // to get the list of a travel tours, travel:slug means the search id of the travel will be the slug (/travels/first-travel/tours).

Route::prefix('/admin')->middleware('auth:sanctum')->group(function () { // after adding this prefix, the routes of this group will be api/v1/admin/...
    Route::post('/travels', [AdminTravelController::class, 'store']); // to post (create) new travel.
});

Route::post('/login', LoginController::class); // The login route, we don't have to call the method by name because its a __invoke method inside the LoginController.
