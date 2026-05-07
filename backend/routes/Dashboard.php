<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::prefix('dashboard')->group(function () {

    // ALL DASHBOARD DATA
    Route::get('/', [DashboardController::class, 'index']);

    // SUMMARY CARD
    Route::get('/summary', [DashboardController::class, 'summary']);

    // CHART
    Route::get('/revenue-chart', [DashboardController::class, 'revenueChart']);

    // TOP
    Route::get('/top-tickets', [DashboardController::class, 'topTickets']);

    Route::get('/top-parks', [DashboardController::class, 'topParks']);

    // TABLE
    Route::get('/latest-bookings', [DashboardController::class, 'latestBookings']);

    Route::get('/latest-payments', [DashboardController::class, 'latestPayments']);

    Route::get('/booking-status', [DashboardController::class, 'bookingStatus']);

    Route::get('/payment-status', [DashboardController::class, 'paymentStatus']);
});