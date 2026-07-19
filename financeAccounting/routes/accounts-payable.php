<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountPayable\SupplierBillController;
use App\Http\Controllers\AccountPayable\PaymentController;

Route::prefix('supplier-bills')->name('supplier-bills.')->group(function () {
    Route::get('/', [SupplierBillController::class, 'index'])->name('index');
    Route::post('/', [SupplierBillController::class, 'store'])->name('store');
    Route::put('/{supplierBill}', [SupplierBillController::class, 'update'])->name('update');
    Route::delete('/{supplierBill}', [SupplierBillController::class, 'destroy'])->name('destroy');
    Route::patch('/{supplierBill}/pay', [SupplierBillController::class, 'pay'])->name('pay');
    Route::patch('/{supplierBill}/approve', [SupplierBillController::class, 'approve'])->name('approve');
    Route::post('/batch-pay', [SupplierBillController::class, 'batchPay'])->name('batch-pay');
    Route::post('/{supplierBill}/attachments', [SupplierBillController::class, 'uploadAttachment'])->name('attachments.upload');
});

Route::get('/attachments/{attachment}/download', [SupplierBillController::class, 'downloadAttachment'])->name('attachments.download');

Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::post('/', [PaymentController::class, 'store'])->name('store');
});
