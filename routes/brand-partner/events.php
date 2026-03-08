<?php

use App\Http\Controllers\BrandPartner\EventController;
use Illuminate\Support\Facades\Route;

Route::resource('events', EventController::class)->except(['show']);

Route::post('events/{event}/toggle-status', [EventController::class, 'toggleStatus'])
    ->name('events.toggle-status');
