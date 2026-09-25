<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListingController;

Route::get('/listings/search', [ListingController::class, 'search']);
Route::apiResource('listings', ListingController::class);
