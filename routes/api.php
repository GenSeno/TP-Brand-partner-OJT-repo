<?php

use App\Http\Controllers\Api\PreOrderController;
use App\Http\Controllers\Api\ProductApprovalController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Store\PaymentController;
use Illuminate\Support\Facades\Route;

/*
 * Incoming callbacks from TPInkAdmin (approval decisions).
 * Authenticated via X-API-Key header.
 */
Route::middleware('api')->group(function () {
    Route::post('/products/{product}/approval-status', [ProductApprovalController::class, 'update'])
        ->name('api.products.approval-status');

    Route::get('/products', [ProductController::class, 'index'])
        ->name('api.products.index');

    Route::get('/products/{product}', [ProductController::class, 'show'])
        ->name('api.products.show');

    Route::get('/pre-orders', [PreOrderController::class, 'index'])
        ->name('api.pre-orders.index');
});

Route::post('/webhook/xendit', [PaymentController::class, 'webhook']);
