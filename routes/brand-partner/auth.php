<?php

use App\Http\Controllers\BrandPartnerAuth\AuthenticatedSessionController;
use App\Http\Controllers\BrandPartnerAuth\NewPasswordController;
use App\Http\Controllers\BrandPartnerAuth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:brand_partner')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth.brand_partner')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
