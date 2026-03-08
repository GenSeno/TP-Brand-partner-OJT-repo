<?php

use App\Constants\StaffPermission;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductOptionController;
use App\Http\Controllers\Admin\SupplierController;
use Illuminate\Support\Facades\Route;

Route::resource('category', CategoryController::class)
    ->middleware(['can:' . StaffPermission::MANAGE_CATEGORIES])
    ->except(['show']);

Route::get(
    'category/{category}/products/',
    [CategoryController::class, 'getProducts']
)->name('category.products');

Route::resource('product', ProductController::class)
    ->middleware(['can:' . StaffPermission::MANAGE_PRODUCTS])
    ->except(['show']);

Route::patch('product/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
    ->name('product.toggle-status');
Route::get(
    'product/{product}/variants/',
    [ProductController::class, 'getVariants']
)->name('product.variants');

Route::get(
    '/products/{product}/variants/by-option/{valueId}',
    [ProductController::class, 'variantsByOption']
)->name('products.variants.by-option');

Route::resource('product-option', ProductOptionController::class)
    ->middleware(['can:' . StaffPermission::MANAGE_PRODUCT_OPTIONS])
    ->except(['create', 'store', 'show']);

Route::resource('supplier', SupplierController::class)
    ->middleware(['can:' . StaffPermission::MANAGE_SUPPLIERS])
    ->except(['show']);

