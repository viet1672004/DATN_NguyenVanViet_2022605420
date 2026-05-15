<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ParkController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminUserController;

// PARKS
Route::post('/parks', [ParkController::class, 'store']);

Route::put('/parks/{id}', [ParkController::class, 'update']);

Route::delete('/parks/{id}', [ParkController::class, 'destroy']);


// TICKETS
Route::post('/tickets', [TicketController::class, 'store']);

Route::put('/tickets/{id}', [TicketController::class, 'update']);

Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);

Route::prefix('admin/users')->group(function () {

    // danh sách user
    Route::get('/', [AdminUserController::class, 'index']);

    // chi tiết user
    Route::get('/{id}', [AdminUserController::class, 'show']);

    // khóa / mở khóa
    Route::put('/{id}/toggle-block', [AdminUserController::class, 'toggleBlock']);

    // xóa user
    Route::delete('/{id}', [AdminUserController::class, 'destroy']);
});