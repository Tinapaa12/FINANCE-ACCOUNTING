<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierBillController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Procurement\PurchaseOrderController as ProcurementPurchaseOrderController;
use App\Http\Controllers\Procurement\GoodsReceiptController;
use App\Http\Controllers\Procurement\MatchingController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return session('auth_logged_in') ? redirect()->route('supplier-bills.index') : redirect()->route('login');
});

Route::middleware('app.auth')->group(function () {
    // Account Payables
    Route::get('/supplier-bills', [SupplierBillController::class, 'index'])->name('supplier-bills.index');
    Route::post('/supplier-bills', [SupplierBillController::class, 'store'])->name('supplier-bills.store');
    Route::delete('/supplier-bills/{supplierBill}', [SupplierBillController::class, 'destroy'])->name('supplier-bills.destroy');
    Route::put('/supplier-bills/{supplierBill}', [SupplierBillController::class, 'update'])->name('supplier-bills.update');
    Route::patch('/supplier-bills/{supplierBill}/pay', [SupplierBillController::class, 'pay'])->name('supplier-bills.pay');
    Route::patch('/supplier-bills/{supplierBill}/approve', [SupplierBillController::class, 'approve'])->name('supplier-bills.approve');
    Route::post('/supplier-bills/batch-pay', [SupplierBillController::class, 'batchPay'])->name('supplier-bills.batch-pay');
    Route::post('/supplier-bills/{supplierBill}/attachments', [SupplierBillController::class, 'uploadAttachment'])->name('supplier-bills.attachments.upload');
    Route::get('/attachments/{attachment}/download', [SupplierBillController::class, 'downloadAttachment'])->name('attachments.download');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

    // Procurement
    Route::get('/procurement/purchase-orders', [ProcurementPurchaseOrderController::class, 'index'])->name('procurement.po.index');
    Route::post('/procurement/purchase-orders', [ProcurementPurchaseOrderController::class, 'store'])->name('procurement.po.store');
    Route::put('/procurement/purchase-orders/{id}', [ProcurementPurchaseOrderController::class, 'update'])->name('procurement.po.update');
    Route::patch('/procurement/purchase-orders/{id}/send', [ProcurementPurchaseOrderController::class, 'send'])->name('procurement.po.send');
    Route::patch('/procurement/purchase-orders/{id}/confirm', [ProcurementPurchaseOrderController::class, 'confirm'])->name('procurement.po.confirm');
    Route::patch('/procurement/purchase-orders/{id}/deliver', [ProcurementPurchaseOrderController::class, 'markDelivered'])->name('procurement.po.deliver');
    Route::patch('/procurement/purchase-orders/{id}/cancel', [ProcurementPurchaseOrderController::class, 'cancel'])->name('procurement.po.cancel');

    Route::get('/procurement/goods-receipts', [GoodsReceiptController::class, 'index'])->name('procurement.gr.index');
    Route::get('/procurement/goods-receipts/create', [GoodsReceiptController::class, 'create'])->name('procurement.gr.create');
    Route::post('/procurement/goods-receipts', [GoodsReceiptController::class, 'store'])->name('procurement.gr.store');
    Route::patch('/procurement/goods-receipts/{id}/complete', [GoodsReceiptController::class, 'complete'])->name('procurement.gr.complete');

    Route::get('/procurement/matching', [MatchingController::class, 'index'])->name('procurement.matching.index');
    Route::post('/procurement/matching', [MatchingController::class, 'match'])->name('procurement.matching.match');
});
