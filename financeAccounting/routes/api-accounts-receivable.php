<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ARController;
use App\Http\Controllers\SalesTransactionController;

Route::get('ar/overview', [ARController::class, 'overview'])->name('api.ar.overview');
Route::get('ar/payments-received', [ARController::class, 'payments'])->name('api.ar.payments');
Route::get('ar/aging-report', [ARController::class, 'aging'])->name('api.ar.aging');
Route::match(['GET', 'POST'], 'ar/aging-report/remind', [ARController::class, 'remindCustomer'])->name('api.ar.aging.remind');
Route::post('sales-transactions', [SalesTransactionController::class, 'store'])->name('api.sales-transactions.store');
Route::post('sales-transactions/{salesTransaction}/mark-as-paid', [SalesTransactionController::class, 'markAsPaid'])->name('api.sales-transactions.mark-as-paid');
