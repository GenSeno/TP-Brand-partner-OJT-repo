<?php

use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Store\BrandPartnerCartController;
use App\Http\Controllers\Store\BrandPartnerCheckoutController;
use App\Http\Controllers\Store\BrandPartnerStoreController;
use Illuminate\Support\Facades\Route;

/**
 * Front Store Routes for Brand Partners
 * These routes should be loaded LAST in web.php to avoid conflicts
 */
Route::group([
    'as' => 'store.',
    'middleware' => [\App\Http\Middleware\HandleStoreInertiaRequests::class],
], function () {

    // Store home page
    Route::get('/', [BrandPartnerStoreController::class, 'index'])
        ->name('brand-partner.index');

    // Product detail page
    Route::get('/product/{productSlug}', [BrandPartnerStoreController::class, 'product'])
        ->name('brand-partner.product');

    // Cart routes
    Route::post('/cart/add', [BrandPartnerCartController::class, 'add'])
        ->name('brand-partner.cart.add');

    Route::get('/cart', [BrandPartnerCartController::class, 'index'])
        ->name('brand-partner.cart');

    Route::patch('/cart/{itemId}', [BrandPartnerCartController::class, 'update'])
        ->name('brand-partner.cart.update');

    Route::delete('/cart/{itemId}', [BrandPartnerCartController::class, 'remove'])
        ->name('brand-partner.cart.remove');

    Route::delete('/cart', [BrandPartnerCartController::class, 'clear'])
        ->name('brand-partner.cart.clear');

    // Checkout routes
    Route::get('/checkout', [BrandPartnerCheckoutController::class, 'index'])
        ->name('brand-partner.checkout');

    Route::post('/checkout', [BrandPartnerCheckoutController::class, 'store'])
        ->name('brand-partner.checkout.store');

    // Address lookup routes (public, used by checkout form)
    Route::get('/address/provinces', [AddressController::class, 'provinces'])->name('address.provinces');
    Route::get('/address/cities', [AddressController::class, 'cities'])->name('address.cities');
    Route::post('/address/states', [AddressController::class, 'states'])->name('address.states');

    // Order confirmation
    Route::get('/order/{reference}', [BrandPartnerCheckoutController::class, 'confirmation'])
        ->name('brand-partner.order.confirmation');

});
