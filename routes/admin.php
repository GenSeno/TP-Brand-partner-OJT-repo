<?php

use App\Http\Controllers\Actions\ModelBulkDestroy;
use App\Http\Controllers\Actions\ModelToggleField;
use App\Http\Controllers\Actions\ServePrivateStorage;
use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {

    Route::redirect('/', '/brand-partner/dashboard');
    Route::redirect('/dashboard', '/brand-partner/dashboard');

    Route::middleware('auth:staff')->group(function () {

        Route::redirect('/', '/admin/dashboard');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::patch('/toggle/{model}/{id}/{field}', ModelToggleField::class)
            ->name('toggle-field');

        Route::delete('/bulk-destroy/{model}', ModelBulkDestroy::class)
            ->name('bulk-destroy');

        Route::get('private-storage/{media}/{filename}', ServePrivateStorage::class)
            ->middleware('signed')
            ->name('private-storage');

        Route::group(['as' => 'address.'], function () {
            Route::post('/states', [AddressController::class, 'states'])
                ->name('states');
            Route::get('/regions', [AddressController::class, 'regions'])
                ->name('regions');
            Route::get('/provinces', [AddressController::class, 'provinces'])
                ->name('provinces');
            Route::get('/cities', [AddressController::class, 'cities'])
                ->name('cities');
            Route::get('/barangays', [AddressController::class, 'barangays'])
                ->name('barangays');
            Route::get('/philippines', [AddressController::class, 'getPhilippines'])
                ->name('philippines');
        });

        require __DIR__.'/admin/catalog.php';

        require __DIR__.'/admin/operations.php';

        require __DIR__.'/admin/sales.php';

        require __DIR__.'/admin/finance.php';

        require __DIR__.'/admin/settings.php';

        require __DIR__.'/admin/brand-partners.php';

    });

    require __DIR__.'/admin/auth.php';

});
