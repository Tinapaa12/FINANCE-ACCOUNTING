<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierBillController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GoodsReceivedNoteController;
use App\Http\Controllers\InventoryController;


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

Route::patch('/supplier-bills/{supplierBill}/pay', [SupplierBillController::class, 'pay'])
    ->name('supplier-bills.pay');

Route::patch('/supplier-bills/{supplierBill}/approve', [SupplierBillController::class, 'approve'])
    ->name('supplier-bills.approve');

Route::post('/supplier-bills/batch-pay', [SupplierBillController::class, 'batchPay'])
    ->name('supplier-bills.batch-pay');

Route::post('/payments', [PaymentController::class, 'store'])
    ->name('payments.store');

Route::post('/supplier-bills/{supplierBill}/attachments', [SupplierBillController::class, 'uploadAttachment'])
    ->name('supplier-bills.attachments.upload');

Route::get('/attachments/{attachment}/download', [SupplierBillController::class, 'downloadAttachment'])
    ->name('attachments.download');

Route::get('/payments', [PaymentController::class, 'index'])
    ->name('payments.index');

Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])
    ->name('purchase-orders.index');

Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])
    ->name('purchase-orders.store');

Route::put('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])
    ->name('purchase-orders.update');

Route::patch('/purchase-orders/{id}/approve', [PurchaseOrderController::class, 'approve'])
    ->name('purchase-orders.approve');

Route::patch('/purchase-orders/{id}/receive', [PurchaseOrderController::class, 'receive'])
    ->name('purchase-orders.receive');

Route::delete('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])
    ->name('purchase-orders.destroy');

Route::get('/goods-received-notes', [GoodsReceivedNoteController::class, 'index'])
    ->name('goods-received-notes.index');

Route::post('/goods-received-notes', [GoodsReceivedNoteController::class, 'store'])
    ->name('goods-received-notes.store');

Route::put('/goods-received-notes/{goodsReceivedNote}', [GoodsReceivedNoteController::class, 'update'])
    ->name('goods-received-notes.update');

Route::patch('/goods-received-notes/{id}/complete', [GoodsReceivedNoteController::class, 'complete'])
    ->name('goods-received-notes.complete');

Route::delete('/goods-received-notes/{goodsReceivedNote}', [GoodsReceivedNoteController::class, 'destroy'])
    ->name('goods-received-notes.destroy');

Route::get('/inventory', [InventoryController::class, 'index'])
    ->name('inventory.index');

Route::post('/inventory', [InventoryController::class, 'store'])
    ->name('inventory.store');

Route::post('/inventory/stock-in', [InventoryController::class, 'stockIn'])
    ->name('inventory.stock-in');
Route::post('/inventory/stock-out', [InventoryController::class, 'stockOut'])
    ->name('inventory.stock-out');
Route::get('/inventory/tracking', [InventoryController::class, 'tracking'])
    ->name('inventory.tracking');
Route::post('/inventory/tracking/receive', [InventoryController::class, 'trackingReceive'])
    ->name('inventory.tracking.receive');
Route::get('/inventory/{id}/purchase', [InventoryController::class, 'purchase'])
    ->name('inventory.purchase');
