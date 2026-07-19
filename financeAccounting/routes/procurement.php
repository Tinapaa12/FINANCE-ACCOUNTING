<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Procurement\PurchaseOrderController;
use App\Http\Controllers\Procurement\GoodsReceiptController;
use App\Http\Controllers\Procurement\MatchingController;

Route::prefix('procurement')->name('procurement.')->group(function () {
    Route::prefix('purchase-orders')->name('po.')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
        Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
        Route::put('/{id}', [PurchaseOrderController::class, 'update'])->name('update');
        Route::patch('/{id}/send', [PurchaseOrderController::class, 'send'])->name('send');
        Route::patch('/{id}/confirm', [PurchaseOrderController::class, 'confirm'])->name('confirm');
        Route::patch('/{id}/deliver', [PurchaseOrderController::class, 'markDelivered'])->name('deliver');
        Route::patch('/{id}/cancel', [PurchaseOrderController::class, 'cancel'])->name('cancel');
    });

    Route::prefix('goods-receipts')->name('gr.')->group(function () {
        Route::get('/', [GoodsReceiptController::class, 'index'])->name('index');
        Route::get('/create', [GoodsReceiptController::class, 'create'])->name('create');
        Route::post('/', [GoodsReceiptController::class, 'store'])->name('store');
        Route::patch('/{id}/complete', [GoodsReceiptController::class, 'complete'])->name('complete');
    });

    Route::prefix('matching')->name('matching.')->group(function () {
        Route::get('/', [MatchingController::class, 'index'])->name('index');
        Route::post('/', [MatchingController::class, 'match'])->name('match');
        Route::patch('/{bill}/resolve', [MatchingController::class, 'resolve'])->name('resolve');
    });
});
