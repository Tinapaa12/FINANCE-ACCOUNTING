<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Procurement\PurchaseOrderController;
use App\Http\Controllers\Procurement\GoodsReceiptController;

Route::name('api.')->apiResource('purchase-orders', PurchaseOrderController::class);
Route::name('api.')->apiResource('goods-receipts', GoodsReceiptController::class);
