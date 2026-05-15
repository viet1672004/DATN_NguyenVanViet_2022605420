<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [UserController::class, 'me']);

    Route::post('/user/update', [UserController::class, 'update']);

    Route::post('/user/change-password', [UserController::class, 'changePassword']);
});