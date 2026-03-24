<?php

use App\Http\Controllers\BrandPartner\OrderController;
use Illuminate\Support\Facades\Route;

Route::resource('orders', OrderController::class)->only(['index', 'show']);

Route::get('orders/{order}/edit', [OrderController::class, 'edit'])
    ->name('orders.edit');

Route::put('orders/{order}', [OrderController::class, 'update'])
    ->name('orders.update');

Route::post('orders/{order}/confirm', [OrderController::class, 'confirm'])
    ->name('orders.confirm');

Route::post('orders/{order}/complete', [OrderController::class, 'complete'])
    ->name('orders.complete');

Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])
    ->name('orders.cancel');

Route::post('orders/{order}/send-to-admin', [OrderController::class, 'sendToAdmin'])
    ->name('orders.send-to-admin');

Route::get('orders/{order}/receive-payment', [OrderController::class, 'receivePaymentForm'])
    ->name('orders.receive-payment');

Route::post('orders/{order}/receive-payment', [OrderController::class, 'receivePayment'])
    ->name('orders.receive-payment.store');
