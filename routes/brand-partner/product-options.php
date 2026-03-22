<?php

use App\Http\Controllers\BrandPartner\ProductOptionController;
use Illuminate\Support\Facades\Route;

Route::resource('product-options', ProductOptionController::class)
    ->except(['show'])
    ->names([
        'index'   => 'product-options.index',
        'create'  => 'product-options.create',
        'store'   => 'product-options.store',
        'edit'    => 'product-options.edit',
        'update'  => 'product-options.update',
        'destroy' => 'product-options.destroy',
    ]);
