<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountPayable\SupplierBillController;
use App\Http\Controllers\AccountPayable\PaymentController;

Route::get('supplier-bills', [SupplierBillController::class, 'index'])->name('api.supplier-bills');
Route::post('supplier-bills', [SupplierBillController::class, 'store'])->name('api.supplier-bills.store');
Route::put('supplier-bills/{supplierBill}', [SupplierBillController::class, 'update'])->name('api.supplier-bills.update');
Route::delete('supplier-bills/{supplierBill}', [SupplierBillController::class, 'destroy'])->name('api.supplier-bills.destroy');
Route::patch('supplier-bills/{supplierBill}/pay', [SupplierBillController::class, 'pay'])->name('api.supplier-bills.pay');
Route::patch('supplier-bills/{supplierBill}/approve', [SupplierBillController::class, 'approve'])->name('api.supplier-bills.approve');
Route::post('supplier-bills/batch-pay', [SupplierBillController::class, 'batchPay'])->name('api.supplier-bills.batch-pay');

Route::get('payments', [PaymentController::class, 'index'])->name('api.payments');
Route::post('payments', [PaymentController::class, 'store'])->name('api.payments.store');
