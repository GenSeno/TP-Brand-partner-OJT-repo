<?php

use App\Http\Controllers\BrandPartner\CategoryController;
use Illuminate\Support\Facades\Route;

Route::resource('categories', CategoryController::class)->except(['show']);

Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])
    ->name('categories.toggle-status');
