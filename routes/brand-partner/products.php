<?php

use App\Http\Controllers\BrandPartner\ProductController;
use App\Http\Controllers\BrandPartner\ProductImageController;
use Illuminate\Support\Facades\Route;

Route::resource('products', ProductController::class);

Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
    ->name('products.toggle-status');

Route::post('products/{product}/images', [ProductImageController::class, 'store'])
    ->name('products.images.store');

Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy'])
    ->name('products.images.destroy');

Route::post('products/{product}/images/{image}/primary', [ProductImageController::class, 'makePrimary'])
    ->name('products.images.primary');
