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

Route::name('api.')->prefix('management')->group(function () {
    Route::post('budget', [\App\Http\Controllers\Api\ManagementBudgetController::class, 'store']);
    Route::get('budget', [\App\Http\Controllers\Api\ManagementBudgetController::class, 'index']);
    Route::delete('budget/{id}', [\App\Http\Controllers\Api\ManagementBudgetController::class, 'destroy'])->name('budget.destroy');
});

Route::post('seed-demo', [\App\Http\Controllers\Api\DemoDataController::class, 'seed']);
Route::post('migrate-fresh', [\App\Http\Controllers\Api\DemoDataController::class, 'migrateFresh']);

Route::middleware('app.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::name('api.')->apiResource('chart-of-accounts', ChartOfAccountsController::class)->parameters(['chart-of-accounts' => 'chartOfAccount']);
    Route::name('api.')->apiResource('journal-entries', JournalEntryController::class)->parameters(['journal-entries' => 'journalEntry']);

    Route::get('supplier-bills', [SupplierBillController::class, 'index'])->name('api.supplier-bills');
    Route::post('supplier-bills', [SupplierBillController::class, 'store'])->name('api.supplier-bills.store');
    Route::put('supplier-bills/{supplierBill}', [SupplierBillController::class, 'update'])->name('api.supplier-bills.update');
    Route::delete('supplier-bills/{supplierBill}', [SupplierBillController::class, 'destroy'])->name('api.supplier-bills.destroy');
    Route::patch('supplier-bills/{supplierBill}/pay', [SupplierBillController::class, 'pay'])->name('api.supplier-bills.pay');
    Route::patch('supplier-bills/{supplierBill}/approve', [SupplierBillController::class, 'approve'])->name('api.supplier-bills.approve');
    Route::post('supplier-bills/batch-pay', [SupplierBillController::class, 'batchPay'])->name('api.supplier-bills.batch-pay');

    Route::get('payments', [PaymentController::class, 'index'])->name('api.payments');
    Route::post('payments', [PaymentController::class, 'store'])->name('api.payments.store');

    Route::name('api.')->apiResource('purchase-orders', PurchaseOrderController::class);
    Route::name('api.')->apiResource('goods-receipts', GoodsReceiptController::class);

    Route::get('ar/overview', [ARController::class, 'overview'])->name('api.ar.overview');
    Route::get('ar/payments-received', [ARController::class, 'payments'])->name('api.ar.payments');
    Route::get('ar/aging-report', [ARController::class, 'aging'])->name('api.ar.aging');
    Route::post('ar/invoices', [ARController::class, 'storeInvoice'])->name('api.ar.invoices.store');

    Route::post('sales-transactions', [SalesTransactionController::class, 'store'])->name('api.sales-transactions.store');
    Route::post('sales-transactions/{salesTransaction}/mark-as-paid', [SalesTransactionController::class, 'markAsPaid'])->name('api.sales-transactions.mark-as-paid');

    Route::get('reports/income', [FinancialReportController::class, 'income'])->name('api.reports.income');
    Route::get('reports/assets', [FinancialReportController::class, 'assets'])->name('api.reports.assets');
    Route::get('reports/budget', [FinancialReportController::class, 'budget'])->name('api.reports.budget');
    Route::get('reports/cashflow', [FinancialReportController::class, 'cashflow'])->name('api.reports.cashflow');
});
