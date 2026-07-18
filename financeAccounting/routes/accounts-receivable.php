<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountsReceivable\ARController;
use App\Http\Controllers\Dummy\SalesTransactionController;

Route::prefix('ar')->name('ar.')->group(function () {
    Route::get('/overview', [ARController::class, 'overview'])->name('overview');
    Route::get('/payments-received', [ARController::class, 'payments'])->name('payments');
    Route::get('/aging-report', [ARController::class, 'aging'])->name('aging');
    Route::post('/invoices', [ARController::class, 'storeInvoice'])->name('invoices.store');
});

Route::prefix('sales-transactions')->name('sales-transactions.')->group(function () {
    Route::get('/', fn() => redirect()->route('sales-transactions.create'))->name('index');
    Route::get('/create', [SalesTransactionController::class, 'create'])->name('create');
    Route::post('/', [SalesTransactionController::class, 'store'])->name('store');
    Route::post('/{salesTransaction}/mark-as-paid', [SalesTransactionController::class, 'markAsPaid'])->name('mark-as-paid');
});
