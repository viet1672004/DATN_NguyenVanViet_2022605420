<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::get('/hello', function () {

    return response()->json([
        'message' => 'Hello từ Laravel'
    ]);
});

/*
|--------------------------------------------------------------------------
| PUBLIC VIEW
|--------------------------------------------------------------------------
*/

require __DIR__.'/park.php';

require __DIR__.'/ticket.php';

require __DIR__.'/blog.php';

require __DIR__.'/chatbot.php';
/*
|--------------------------------------------------------------------------
| VNPAY RETURN
|--------------------------------------------------------------------------
*/

// 🔥 PUBLIC
Route::get('/vnpay/return', [PaymentController::class, 'vnpayReturn']);

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {

        return $request->user()->load('role');
    });

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER + ADMIN
    |--------------------------------------------------------------------------
    */

    require __DIR__.'/booking.php';

    require __DIR__.'/payment.php';

    require __DIR__.'/user.php';

    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {

        require __DIR__.'/admin.php';

        require __DIR__.'/dashboard.php';
    });
});