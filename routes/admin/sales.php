<?php

use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\InvoicePaymentController;
use App\Http\Controllers\Admin\OrderAddressController;
use App\Http\Controllers\Admin\PaymentAcknowledgementController;
use App\Http\Controllers\Admin\OrderInvoiceController;
use App\Http\Controllers\Admin\OrderItemController;
use App\Http\Controllers\Admin\OrderShippingController;
use Illuminate\Support\Facades\Route;
use App\Constants\StaffPermission;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\QuoteController;
use App\Http\Controllers\Admin\QuoteLineController;
use App\Http\Controllers\Admin\InvoiceShippingController;

// Define admin sales routes here
Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');

    Route::get('/create', [CustomerController::class, 'create'])->name('create');
    Route::post('/', [CustomerController::class, 'store'])->name('store');

    Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');

    Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
    Route::post('/{customer}', [CustomerController::class, 'update'])->name('update');

    Route::post('/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('toggle-status');

    Route::delete('/bulk-destroy', [CustomerController::class, 'bulkDestroy'])->name('bulk-destroy');
    Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
    Route::get('/search/{search}', [CustomerController::class, 'search'])->name('search');

});

Route::resource('quotation', QuoteController::class)
    ->middleware(['can:' . StaffPermission::MANAGE_QUOTES]);

Route::group(['prefix' => 'quotation', 'as' => 'quotation.'], function () {
    Route::post('/{quotation}/toggle-status', [QuoteController::class, 'toggleStatus'])->name('toggle-status');
    Route::delete('/bulk-destroy', [QuoteController::class, 'bulkDestroy'])->name('bulk-destroy');

    Route::get('/item/{quotation}', [QuoteController::class, 'item'])->name('item');
    Route::get('/{quotation}/edit_date', [QuoteController::class, 'edit_date'])->name('edit_date');
    Route::post('/{quotation}/update_date', [QuoteController::class, 'update_date'])->name('update_date');
    Route::get('/{quotation}/adjustment', [QuoteController::class, 'adjustment'])->name('adjustment');
    Route::post('/{quotation}/store', [QuoteController::class, 'storeAdjustment'])->name('store.adjustment');
    Route::post('/{quotation}/completed', [QuoteController::class, 'completed'])->name('completed');
    Route::post('/{quotation}/draft', [QuoteController::class, 'draft'])->name('draft');
    Route::post('/{quotation}/cancelled', [QuoteController::class, 'cancelled'])->name('cancelled');
    Route::post('/{quotation}/create-order', [QuoteController::class, 'createOrder'])->name('create-order');
    Route::post('/{quotation}/share', [QuoteController::class, 'share'])->name('share');
    Route::post('/{quotation}/send', [QuoteController::class, 'send'])->name('send');
    Route::get('/status/{status}', [QuoteController::class, 'fetchByStatus'])->name('status');


    /*QuoteLineController*/
    Route::post('/{quotation}/lines', [QuoteLineController::class, 'store'])->name('lines');
    Route::get('/{quotation}/add', [QuoteLineController::class, 'create'])->name('add');
    Route::get('/{quotation}/lines/{product}/edit', [QuoteLineController::class, 'edit_product'])->name('lines.edit.product');
    Route::post('/{quotation}/lines/{product}', [QuoteLineController::class, 'update_bulk'])->name('lines.update_bulk');
    Route::delete('/lines/bulk-destroy', [QuoteLineController::class, 'bulkDestroy'])->name('lines.bulk-destroy');
    Route::delete('/lines/{line}', [QuoteLineController::class, 'destroy'])->name('lines.destroy');

    // Route::get('/{quotation}/lines/{line}/edit', [QuoteLineController::class, 'edit'])->name('lines.edit');
    // Route::post('/{quotation}/lines/{line}', [QuoteLineController::class, 'update'])->name('lines.update');

    Route::group(['prefix' => '{quotation}/media', 'as' => 'media.'], function () {
        Route::post('/', [QuoteController::class, 'upload'])
            ->name('upload');
        Route::delete('/{mediaId}', [QuoteController::class, 'destroyMedia'])
            ->name('destroy');
    });

    Route::post('/{quotation}/note', [QuoteController::class, 'addNote'])
        ->name('note');
});

Route::resource('order', OrderController::class)
    ->middleware(['can:' . StaffPermission::MANAGE_ORDERS])
    ->except(['edit']);

Route::get('order/status/{status}', [OrderController::class, 'fetchByStatus'])
    ->middleware(['can:' . StaffPermission::MANAGE_ORDERS])
    ->name('order.status');

Route::group(['prefix' => 'order/{order}', 'as' => 'order.'], function () {

    Route::group(['prefix' => 'address', 'as' => 'address.'], function () {
        Route::get('/', [OrderAddressController::class, 'edit'])
            ->name('edit');
        Route::put('/', [OrderAddressController::class, 'update'])
            ->name('update');
    });

    Route::resource('billing', OrderInvoiceController::class)
        ->only(['create', 'store']);

    // Route::group(['prefix' => 'shipping', 'as' => 'shipping.'], function () {
    //     Route::get('/edit', [OrderShippingController::class, 'edit'])
    //         ->name('edit');
    //     Route::put('/', [OrderShippingController::class, 'update'])
    //         ->name('update');
    // });

    Route::resource('item', OrderItemController::class)
        ->except(['index', 'show']);

    Route::get('/item/{product}/edit-product', [OrderItemController::class, 'editProduct'])
        ->name('item.edit-product');
    Route::post('/item/{product}/update-bulk', [OrderItemController::class, 'updateBulk'])
        ->name('item.update-bulk');
    Route::delete('/item/bulk-destroy', [OrderItemController::class, 'bulkDestroy'])
        ->name('item.bulk-destroy');

    Route::post('/job-order/create', [OrderController::class, 'createJobOrder'])
        ->name('job-order.create');

    Route::post('/cancel', [OrderController::class, 'cancel'])
        ->name('cancel');

    Route::group(['prefix' => 'media', 'as' => 'media.'], function () {
        Route::post('/', [OrderController::class, 'upload'])
            ->name('upload');
        Route::delete('/{mediaId}', [OrderController::class, 'destroyMedia'])
            ->name('destroy');
    });

    Route::post('/note', [OrderController::class, 'addNote'])
        ->name('note');

})->middleware(['can:' . StaffPermission::MANAGE_ORDERS]);


Route::resource('billing', InvoiceController::class)
    ->middleware(['can:' . StaffPermission::MANAGE_INVOICES])
    ->only(['index', 'show']);

Route::get('billing/status/{status}', [InvoiceController::class, 'fetchByStatus'])
    ->middleware(['can:' . StaffPermission::MANAGE_INVOICES])
    ->name('billing.status');

Route::group(['prefix' => 'billing', 'as' => 'billing.'], function () {
    Route::get('/customer/{customer}', [InvoiceController::class, 'showCustomerBillingSummary'])
        ->name('customer');

    Route::group(['prefix' => '{billing}/payment', 'as' => 'payment.'], function () {
        Route::get('/', [InvoicePaymentController::class, 'create'])
            ->name('create');
        Route::post('/', [InvoicePaymentController::class, 'store'])
            ->name('store');
        Route::get('/edit/{payment}', [InvoicePaymentController::class, 'edit'])
            ->name('edit');
        Route::post('/{payment}', [InvoicePaymentController::class, 'update'])
            ->name('update');
        Route::post('/send/{payment}', [InvoicePaymentController::class, 'sendReceipt'])
            ->name('send');
       
    });

    Route::group(['prefix' => 'payment/{payment}/media', 'as' => 'payment.media.'], function () {
        Route::post('/', [InvoicePaymentController::class, 'upload'])
            ->name('upload');
        Route::delete('/{mediaId}', [InvoicePaymentController::class, 'destroyMedia'])
            ->name('destroy');
          Route::get('/{media}', [InvoicePaymentController::class, 'viewMedia'])
            ->name('view');
    });

    Route::group(['prefix' => '{billing}/media', 'as' => 'media.'], function () {
        Route::post('/', [InvoiceController::class, 'upload'])
            ->name('upload');
        Route::delete('/{mediaId}', [InvoiceController::class, 'destroyMedia'])
            ->name('destroy');
    });

    Route::post('/{billing}/send-receipt/{payment}', [InvoiceController::class, 'sendReceipt'])
        ->name('send-receipt');

    Route::post('/{billing}/share', [InvoiceController::class, 'share'])
        ->name('share');

    Route::post('/{billing}/note', [InvoiceController::class, 'addNote'])
        ->name('note');

});


Route::group(['prefix' => 'shipping', 'as' => 'shipping.'], function () {
    Route::get('/edit/{invoice}', [InvoiceShippingController::class, 'edit'])
        ->name('edit');
    Route::put('/{invoice}', [InvoiceShippingController::class, 'update'])
        ->name('update');
});

Route::get('payment-acknowledgement', [PaymentAcknowledgementController::class, 'index'])
    ->middleware(['can:' . StaffPermission::MANAGE_INVOICES])
    ->name('payment-acknowledgement.index');
