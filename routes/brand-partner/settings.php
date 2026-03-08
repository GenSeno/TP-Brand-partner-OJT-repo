<?php

use App\Http\Controllers\BrandPartner\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('settings', [SettingsController::class, 'index'])
    ->name('settings.index');

Route::put('settings', [SettingsController::class, 'update'])
    ->name('settings.update');

Route::put('settings/password', [SettingsController::class, 'updatePassword'])
    ->name('settings.password');

Route::post('settings/logo', [SettingsController::class, 'updateLogo'])
    ->name('settings.logo');
