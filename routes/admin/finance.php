<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ExpenseLineController;
use App\Http\Controllers\Admin\ExpenseAccountController;
use App\Http\Controllers\Admin\CashflowController;
use App\Http\Controllers\Admin\CashflowAdjustmentController;

// Define admin finance routes here
Route::group(['prefix' => 'expense', 'as' => 'expense.'], function () {
    Route::get('/', [ExpenseController::class, 'index'])->name('index');
    Route::get('/create', [ExpenseController::class, 'create'])->name('create');
    Route::post('/', [ExpenseController::class, 'store'])->name('store');
    Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
    Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
    Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
    Route::get('/item/{expense}', [ExpenseController::class, 'item'])->name('item');
    Route::post('/{expense}', [ExpenseController::class, 'update'])->name('update');
    Route::post('/{expense}/cancelled', [ExpenseController::class, 'cancelled'])->name('cancelled');
    Route::get('/{expense}/paid', [ExpenseController::class, 'paid'])->name('paid');
    Route::post('/{expense}/markAsPaid', [ExpenseController::class, 'markAsPaid'])->name('markAsPaid');
    Route::get('/status/{status}', [ExpenseController::class, 'fetchByStatus'])->name('status');

    Route::post('/{expense}/lines', [ExpenseLineController::class, 'store'])->name('lines.store');
    Route::delete('/lines/{expenseLine}', [ExpenseLineController::class, 'destroy'])->name('lines.destroy');

    Route::get('/{expense}/adjustment', [ExpenseController::class, 'adjustment'])->name('adjustment');
    Route::post('/{expense}/store', [ExpenseController::class, 'storeAdjustment'])->name('store.adjustment');
    Route::get('/{expense}/pdf', [ExpenseController::class, 'download'])->name('pdf.download');
    Route::get('/{expense}/print', [ExpenseController::class, 'print'])->name('expenses.pdf.print');
    Route::post('/{expense}/upload', [ExpenseController::class, 'upload'])->name('upload');
    Route::delete('/{expense}/media/{media}', [ExpenseController::class, 'destroyMedia'])->name('media.destroy');

    Route::post('/{expense}/note', [ExpenseController::class, 'addNote'])
        ->name('note');
});

Route::group(['prefix' => 'expense_account', 'as' => 'expense_account.'], function () {
    Route::get('/', [ExpenseAccountController::class, 'index'])->name('index');
    Route::get('/create', [ExpenseAccountController::class, 'create'])->name('create');
    Route::post('/', [ExpenseAccountController::class, 'store'])->name('store');
    Route::post('/{account}/toggle-status', [ExpenseAccountController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/{account}/edit', [ExpenseAccountController::class, 'edit'])->name('edit');
    Route::post('/{account}', [ExpenseAccountController::class, 'update'])->name('update');
    Route::delete('/{expense}', [ExpenseAccountController::class, 'destroy'])->name('destroy');

});

Route::group(['prefix' => 'cashflow', 'as' => 'cashflow.'], function () {
    Route::get('/', [CashflowController::class, 'index'])->name('index');
    Route::get('/{group}', [CashflowController::class, 'show'])->name('show');

    // Payment
    Route::get('/post/payment/{payment}/modal', [CashflowController::class, 'postPaymentModal'])
        ->name('post.payment.modal');
    Route::post('/post/payment/{payment}', [CashflowController::class, 'postPayment'])
        ->name('post.payment');

    // Expenses
    Route::get('/post/expense/{expense}/modal', [CashflowController::class, 'postExpenseModal'])
        ->name('post.expense.modal');
    Route::post('/post/expense/{expense}', [CashflowController::class, 'postExpense'])
        ->name('post.expense');

    Route::post('/payment/{payment}/unpost', [CashflowController::class, 'unpostPayment'])->name('unpost.payment');
    Route::post('/expense/{expense}/unpost', [CashflowController::class, 'unpostExpense'])->name('unpost.expense');

    Route::get('/{group}/adjustment/modal', [CashflowController::class, 'adjustmentModal'])->name('adjustment.modal');
    Route::post('/cashflow/adjustments', [CashflowAdjustmentController::class, 'store'])->name('store.adjustment');
    Route::delete('/adjustment/{adjustment}', [CashflowAdjustmentController::class, 'destroy'])->name('destroy.adjustment');

});

