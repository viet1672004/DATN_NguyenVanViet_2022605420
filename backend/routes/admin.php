<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ParkController;
use App\Http\Controllers\TicketController;

// PARKS
Route::post('/parks', [ParkController::class, 'store']);

Route::put('/parks/{id}', [ParkController::class, 'update']);

Route::delete('/parks/{id}', [ParkController::class, 'destroy']);


// TICKETS
Route::post('/tickets', [TicketController::class, 'store']);

Route::put('/tickets/{id}', [TicketController::class, 'update']);

Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);