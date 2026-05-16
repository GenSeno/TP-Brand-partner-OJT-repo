<?php

use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Store\AuthController;
use App\Http\Controllers\Store\BrandPartnerAboutController;
use App\Http\Controllers\Store\BrandPartnerCartController;
use App\Http\Controllers\Store\BrandPartnerCheckoutController;
use App\Http\Controllers\Store\BrandPartnerCollectionController;
use App\Http\Controllers\Store\BrandPartnerContactController;
use App\Http\Controllers\Store\BrandPartnerPartnerController;
use App\Http\Controllers\Store\BrandPartnerShopController;
use App\Http\Controllers\Store\BrandPartnerStoreController;
use App\Http\Controllers\Store\BrandPartnerWishlistController as WishlistController;
use App\Http\Controllers\Store\UserAddressController;
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

    // Shop page
    Route::get('/shop', [BrandPartnerShopController::class, 'index'])
        ->name('brand-partner.shop');

    // Product detail page
    Route::get('/product/{productSlug}', [BrandPartnerStoreController::class, 'product'])
        ->name('brand-partner.product');

    // Collections page
    Route::get('/collections', [BrandPartnerCollectionController::class, 'index'])
        ->name('brand-partner.collections');

    // About Us page
    Route::get('/about', [BrandPartnerAboutController::class, 'index'])
        ->name('brand-partner.about');

    // Contact Us page
    Route::get('/contact', [BrandPartnerContactController::class, 'index'])
        ->name('brand-partner.contact');

    // Be Our Partner page
    Route::get('/be-our-partner', [BrandPartnerPartnerController::class, 'index'])
        ->name('brand-partner.partner');

    // Auth routes
    Route::post('/login', [AuthController::class, 'login'])
        ->name('brand-partner.login.submit');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('brand-partner.register.submit');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('brand-partner.logout');

    // User account page
    Route::get('/account', [AuthController::class, 'account'])
        ->name('brand-partner.account')
        ->middleware('auth');

    // Update user profile
    Route::patch('/account/profile', [AuthController::class, 'updateProfile'])
        ->name('brand-partner.account.update')
        ->middleware('auth');

    Route::patch('/account/email', [AuthController::class, 'updateEmail'])
        ->name('brand-partner.account.email')
        ->middleware('auth');

    Route::patch('/account/password', [AuthController::class, 'updatePassword'])
        ->name('brand-partner.account.password')
        ->middleware('auth');

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

    // Address lookup routes
    Route::get('/address/provinces', [AddressController::class, 'provinces'])->name('address.provinces');
    Route::get('/address/cities', [AddressController::class, 'cities'])->name('address.cities');
    Route::post('/address/states', [AddressController::class, 'states'])->name('address.states');

    // Order confirmation
    Route::get('/order/{reference}', [BrandPartnerCheckoutController::class, 'confirmation'])
        ->name('brand-partner.order.confirmation');

    Route::patch('/order/{reference}/cancel', [AuthController::class, 'cancelOrder'])
        ->name('brand-partner.order.cancel')
        ->middleware('auth');

    // Wishlist routes
    Route::middleware('auth')->group(function () {

        Route::get('account/wishlist', [WishlistController::class, 'index'])
            ->name('brand-partner.wishlist');

        Route::post('account/wishlist/toggle', [WishlistController::class, 'toggle'])
            ->name('brand-partner.wishlist.toggle');

        Route::delete('account/wishlist/{itemId}', [WishlistController::class, 'remove'])
            ->name('brand-partner.wishlist.remove');

    });

    // User Addresses
    Route::post('/account/addresses', [UserAddressController::class, 'store'])
        ->name('brand-partner.addresses.store')
        ->middleware('auth');
    Route::patch('/account/addresses/{address}', [UserAddressController::class, 'update'])
        ->name('brand-partner.addresses.update')
        ->middleware('auth');
    Route::delete('/account/addresses/{address}', [UserAddressController::class, 'destroy'])
        ->name('brand-partner.addresses.destroy')
        ->middleware('auth');
});

