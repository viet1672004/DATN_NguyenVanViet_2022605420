<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::prefix('payments')->group(function () {

    Route::post('/pay', [PaymentController::class, 'pay']);

    Route::get('/', [PaymentController::class, 'index']);

    Route::get('/{id}', [PaymentController::class, 'show']);

    // VNPAY
    Route::post('/vnpay/create', [PaymentController::class, 'createVnpay']);

    Route::get('/{id}/invoice', [PaymentController::class, 'exportInvoice']);
});
