<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GeneralLedger\ChartOfAccountsController;
use App\Http\Controllers\GeneralLedger\JournalEntryController;
use App\Http\Controllers\AccountPayable\SupplierBillController;
use App\Http\Controllers\AccountPayable\PaymentController;
use App\Http\Controllers\Procurement\PurchaseOrderController;
use App\Http\Controllers\Procurement\GoodsReceiptController;
use App\Http\Controllers\ARController;
use App\Http\Controllers\SalesTransactionController;
use App\Http\Controllers\FinancialReporting\FinancialReportController;
use App\Http\Controllers\DashboardController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('app.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::apiResource('chart-of-accounts', ChartOfAccountsController::class)->parameters(['chart-of-accounts' => 'chartOfAccount']);
    Route::apiResource('journal-entries', JournalEntryController::class)->parameters(['journal-entries' => 'journalEntry']);

    Route::get('supplier-bills', [SupplierBillController::class, 'index']);
    Route::post('supplier-bills', [SupplierBillController::class, 'store']);
    Route::put('supplier-bills/{supplierBill}', [SupplierBillController::class, 'update']);
    Route::delete('supplier-bills/{supplierBill}', [SupplierBillController::class, 'destroy']);
    Route::patch('supplier-bills/{supplierBill}/pay', [SupplierBillController::class, 'pay']);
    Route::patch('supplier-bills/{supplierBill}/approve', [SupplierBillController::class, 'approve']);
    Route::post('supplier-bills/batch-pay', [SupplierBillController::class, 'batchPay']);

    Route::get('payments', [PaymentController::class, 'index']);
    Route::post('payments', [PaymentController::class, 'store']);

    Route::apiResource('purchase-orders', PurchaseOrderController::class);
    Route::apiResource('goods-receipts', GoodsReceiptController::class);

    Route::get('ar/overview', [ARController::class, 'overview']);
    Route::get('ar/payments-received', [ARController::class, 'payments']);
    Route::get('ar/aging-report', [ARController::class, 'aging']);
    Route::post('ar/invoices', [ARController::class, 'storeInvoice']);

    Route::post('sales-transactions', [SalesTransactionController::class, 'store']);
    Route::post('sales-transactions/{salesTransaction}/mark-as-paid', [SalesTransactionController::class, 'markAsPaid']);

    Route::get('reports/income', [FinancialReportController::class, 'income']);
    Route::get('reports/assets', [FinancialReportController::class, 'assets']);
    Route::get('reports/liabilities', [FinancialReportController::class, 'liabilities']);
    Route::get('reports/cashflow', [FinancialReportController::class, 'cashflow']);
});
