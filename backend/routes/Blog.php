<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

// PUBLIC

Route::prefix('blogs')->group(function () {

    Route::get('/', [BlogController::class, 'index']);

    Route::get('/{id}', [BlogController::class, 'show']);
});

// ADMIN

Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('blogs')
    ->group(function () {

        Route::post('/', [BlogController::class, 'store']);

        Route::put('/{id}', [BlogController::class, 'update']);

        Route::delete('/{id}', [BlogController::class, 'destroy']);
    });