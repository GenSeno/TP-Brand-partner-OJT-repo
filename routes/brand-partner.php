<?php

use App\Http\Controllers\BrandPartner\DashboardController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'brand-partner',
    'as' => 'brand-partner.',
    'middleware' => [\App\Http\Middleware\HandleBrandPartnerInertiaRequests::class],
], function () {

    Route::middleware(['auth.brand_partner', 'brand_partner.active'])->group(function () {

        Route::redirect('/', '/brand-partner/dashboard');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        require __DIR__ . '/brand-partner/categories.php';

        require __DIR__ . '/brand-partner/events.php';

        require __DIR__ . '/brand-partner/products.php';

        require __DIR__ . '/brand-partner/orders.php';

        require __DIR__ . '/brand-partner/settings.php';

    });

    require __DIR__ . '/brand-partner/auth.php';

});
