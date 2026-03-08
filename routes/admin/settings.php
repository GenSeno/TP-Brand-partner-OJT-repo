<?php

use App\Constants\StaffPermission;
use App\Http\Controllers\Admin\AccessControl;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Define admin settings routes here
Route::resource('staff', StaffController::class)
    ->middleware(['can:' . StaffPermission::MANAGE_STAFF])
    ->except(['show']);

Route::resource('employee', EmployeeController::class)
    ->middleware(['can:' . StaffPermission::MANAGE_EMPLOYEES])
    ->except(['show']);

Route::middleware(['can:' . StaffPermission::MANAGE_ROLES])
    ->prefix('access-control')
    ->name('access-control.')
    ->group(function () {

        Route::get('/', [AccessControl::class, 'index'])
            ->name('index');

        Route::post('/toggle-permission', [AccessControl::class, 'togglePermission'])
            ->name('toggle-permission');

        Route::resource('role', RoleController::class)
            ->except(['index', 'show']);
    });

Route::resource('users', UserController::class)
    ->middleware(['can:' . StaffPermission::MANAGE_USERS])
    ->except(['show']);
