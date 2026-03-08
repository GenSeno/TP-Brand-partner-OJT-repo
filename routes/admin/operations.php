<?php

use App\Http\Controllers\Admin\InventoryItemController;
use App\Http\Controllers\Admin\JobOrderController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

// Inventory Items (Raw Materials)
Route::resource('inventory-item', InventoryItemController::class)
    ->except(['show']);

Route::group(['prefix' => 'inventory-item', 'as' => 'inventory-item.'], function () {
    Route::get('/{inventoryItem}/adjust-stock', [InventoryItemController::class, 'adjustStock'])
        ->name('adjust-stock');
    Route::post('/{inventoryItem}/process-adjustment', [InventoryItemController::class, 'processAdjustment'])
        ->name('process-adjustment');
    Route::get('/{inventoryItem}/history', [InventoryItemController::class, 'history'])
        ->name('history');
});

// Purchase Orders
Route::resource('purchase-order', PurchaseOrderController::class)
    ->except(['show']);
Route::group(['prefix' => 'purchase-order', 'as' => 'purchase-order.'], function () {
    Route::get('/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])
        ->name('receive');
    Route::post('/{purchaseOrder}/received', [PurchaseOrderController::class, 'received'])
        ->name('received');
});

// Job Orders
Route::resource('job-order', JobOrderController::class)
    ->only(['index', 'edit', 'update']);

Route::group(['prefix' => 'job-order', 'as' => 'job-order.'], function () {
    Route::get('/{jobOrder}/set-produced/{orderLine}', [JobOrderController::class, 'setProduced'])
        ->name('set-produced');
    Route::get('/{jobOrder}/complete-produced/{orderLine}', [JobOrderController::class, 'completeProduced'])
        ->name('complete-produced');
    Route::post('/{jobOrder}/update-produced', [JobOrderController::class, 'updateProduced'])
        ->name('update-produced');
    Route::post('/{jobOrder}/submit-stage', [JobOrderController::class, 'submitStage'])
        ->name('submit-stage');
    Route::post('/{jobOrder}/set-inventory-usage', [JobOrderController::class, 'setInventoryUsage'])
        ->name('set-inventory-usage');
    Route::post('/{jobOrder}/note', [JobOrderController::class, 'addNote'])
        ->name('note');
    Route::post('/{jobOrder}/assign-artist', [JobOrderController::class, 'assignArtist'])
        ->name('assign-artist');

    Route::get('/assign-sewer/{orderLine}', [JobOrderController::class, 'assignSewerView'])
        ->name('assign-sewer');
    Route::post('/assign-sewer/{orderLine}', [JobOrderController::class, 'assignSewer'])
        ->name('assign-sewer.store');
});

