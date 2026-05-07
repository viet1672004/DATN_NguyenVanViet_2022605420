<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkController;

// PUBLIC
Route::get('/parks', [ParkController::class, 'index']);

Route::get('/parks/{id}', [ParkController::class, 'show']);