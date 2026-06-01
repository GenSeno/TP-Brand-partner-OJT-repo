<?php

use App\Http\Controllers\Api\ProductApprovalController;
use App\Http\Controllers\Store\PaymentController;
use Illuminate\Support\Facades\Route;

/*
 * Incoming callbacks from TPInkAdmin (approval decisions).
 * Authenticated via X-API-Key header.
 */
Route::middleware('api')->group(function () {
    Route::post('/products/{product}/approval-status', [ProductApprovalController::class, 'update'])
        ->name('api.products.approval-status');
});

Route::post('/webhook/xendit', [PaymentController::class, 'webhook']);