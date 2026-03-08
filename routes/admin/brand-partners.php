<?php

use App\Http\Controllers\Admin\BrandPartnerController;
use Illuminate\Support\Facades\Route;

Route::resource('brand-partners', BrandPartnerController::class);

Route::post('brand-partners/{brand_partner}/approve', [BrandPartnerController::class, 'approve'])
    ->name('brand-partners.approve');

Route::post('brand-partners/{brand_partner}/suspend', [BrandPartnerController::class, 'suspend'])
    ->name('brand-partners.suspend');

Route::post('brand-partners/{brand_partner}/toggle-status', [BrandPartnerController::class, 'toggleStatus'])
    ->name('brand-partners.toggle-status');
