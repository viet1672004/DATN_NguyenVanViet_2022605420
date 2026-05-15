<?php

use App\Http\Controllers\ChatBotController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('chatbot')
    ->group(function () {

        Route::post('/', [
            ChatBotController::class,
            'store'
        ]);

    });