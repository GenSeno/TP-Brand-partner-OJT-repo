<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\QuoteController;
use App\Http\Controllers\Admin\ExpenseController;
use Illuminate\Support\Facades\Mail;
Route::get('/shared/{token}',[QuoteController::class, 'sharedView'])->name('quotation.shared');
Route::get('/pdf/{quotation}', [QuoteController::class, 'pdf'])->name('pdf');
Route::get('/test/{quotation}', [QuoteController::class, 'test'])->name('test');
Route::get('/{quotation}/download', [QuoteController::class, 'download'])->name('download.quotation');
Route::get('/billing/shared/{token}', [InvoiceController::class, 'sharedView'])->name('billing.shared');
Route::get('/billing/{billing}/download', [InvoiceController::class, 'download'])->name('download.billing');



// Route::get('/test-mail', function() {
//     Mail::raw('This is a test email from Laravel + Mailchimp SMTP', function ($message) {
//         $message->to('bjohnalou@gmail.com')
//                 ->subject('Test Mailchimp Email');
//     });

//     return 'Email sent (check inbox/spam)!';
// });


// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__ . '/admin.php';

require __DIR__ . '/brand-partner.php';

// Store routes must be loaded LAST to avoid conflicts with catch-all slug pattern
require __DIR__ . '/store.php';

// require __DIR__ . '/auth.php';
