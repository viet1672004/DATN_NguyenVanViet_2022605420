<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

// PUBLIC
Route::get('/tickets', [TicketController::class, 'index']);

Route::get('/tickets/{id}', [TicketController::class, 'show']);

Route::get('/tickets-dropdown', [TicketController::class, 'dropdown']);