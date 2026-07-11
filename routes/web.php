<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierBillController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/supplier-bills', [SupplierBillController::class, 'index'])
    ->name('supplier-bills.index');

Route::post('/supplier-bills', [SupplierBillController::class, 'store'])
    ->name('supplier-bills.store');

Route::delete('/supplier-bills/{supplierBill}', [SupplierBillController::class, 'destroy'])
    ->name('supplier-bills.destroy');

Route::put('/supplier-bills/{supplierBill}', [SupplierBillController::class, 'update'])
    ->name('supplier-bills.update');

Route::get('/payments', [PaymentController::class, 'index'])
    ->name('payments.index');
