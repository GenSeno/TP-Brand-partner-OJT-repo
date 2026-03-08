<?php

use App\Http\Controllers\BrandPartner\OrderController;
use Illuminate\Support\Facades\Route;

Route::resource('orders', OrderController::class)->only(['index', 'show']);

Route::post('orders/{order}/confirm', [OrderController::class, 'confirm'])
    ->name('orders.confirm');

Route::post('orders/{order}/complete', [OrderController::class, 'complete'])
    ->name('orders.complete');

Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])
    ->name('orders.cancel');
