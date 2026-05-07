<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

Route::prefix('bookings')->group(function () {

    Route::get('/', [BookingController::class, 'index']);

    Route::get('/{id}', [BookingController::class, 'show']);

    Route::post('/', [BookingController::class, 'store']);

    Route::put('/{id}', [BookingController::class, 'update']);

    Route::delete('/{id}', [BookingController::class, 'destroy']);

    Route::post('/{id}/cancel', [BookingController::class, 'cancel']);
});