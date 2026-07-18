<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GeneralLedger\ChartOfAccountsController;
use App\Http\Controllers\GeneralLedger\JournalEntryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialReporting\FinancialReportController;
use App\Http\Controllers\FinancialReporting\TaxComplianceController;
use App\Http\Controllers\FinancialReporting\ManageDataController;
use App\Http\Controllers\ARController;
use App\Http\Controllers\AccountPayable\SupplierBillController;
use App\Http\Controllers\AccountPayable\PaymentController;

use App\Http\Controllers\Procurement\PurchaseOrderController as ProcurementPurchaseOrderController;
use App\Http\Controllers\Procurement\GoodsReceiptController;
use App\Http\Controllers\Procurement\MatchingController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return session('auth_logged_in') ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('app.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/chart-of-accounts/pdf', [ChartOfAccountsController::class, 'pdf'])->name('chart-of-accounts.pdf');
    Route::resource('chart-of-accounts', ChartOfAccountsController::class)->parameters([
        'chart-of-accounts' => 'chartOfAccount'
    ]);

    Route::get('/journal-entries/pdf', [JournalEntryController::class, 'pdf'])->name('journal-entries.pdf');
    Route::resource('journal-entries', JournalEntryController::class)->parameters([
        'journal-entries' => 'journalEntry'
    ]);

    Route::get('/reports/income', [FinancialReportController::class, 'income'])->name('reports.income');
    Route::get('/reports/assets', [FinancialReportController::class, 'assets'])->name('reports.assets');
    Route::get('/reports/liabilities', [FinancialReportController::class, 'liabilities'])->name('reports.liabilities');
    Route::get('/reports/cashflow', [FinancialReportController::class, 'cashflow'])->name('reports.cashflow');

    Route::get('/reports/income/pdf', [FinancialReportController::class, 'incomePdf'])->name('reports.income.pdf');
    Route::get('/reports/assets/pdf', [FinancialReportController::class, 'assetsPdf'])->name('reports.assets.pdf');
    Route::get('/reports/liabilities/pdf', [FinancialReportController::class, 'liabilitiesPdf'])->name('reports.liabilities.pdf');
    Route::get('/reports/cashflow/pdf', [FinancialReportController::class, 'cashflowPdf'])->name('reports.cashflow.pdf');

    Route::get('/tax-compliance', [TaxComplianceController::class, 'index'])->name('tax.compliance');
    Route::get('/tax-compliance/pdf', [TaxComplianceController::class, 'pdf'])->name('tax.compliance.pdf');

    Route::get('/reports/manage', [ManageDataController::class, 'index'])->name('reports.manage');
    Route::post('/reports/manage/store-budget', [ManageDataController::class, 'storeBudget'])->name('reports.manage.store-budget');
    Route::delete('/reports/manage/budget/{budgetVsActual}', [ManageDataController::class, 'destroyBudget'])->name('reports.manage.destroy-budget');
    Route::post('/reports/manage/store-tax', [ManageDataController::class, 'storeTaxRecord'])->name('reports.manage.store-tax');
    Route::delete('/reports/manage/tax/{taxRecord}', [ManageDataController::class, 'destroyTaxRecord'])->name('reports.manage.destroy-tax');

    // Accounts Receivable
    Route::get('/ar/overview', [ARController::class, 'overview'])->name('ar.overview');
    Route::get('/ar/payments-received', [ARController::class, 'payments'])->name('ar.payments');
    Route::get('/ar/aging-report', [ARController::class, 'aging'])->name('ar.aging');
    Route::post('/ar/invoices', [ARController::class, 'storeInvoice'])->name('ar.invoices.store');

    Route::get('/sales-transactions', fn() => redirect()->route('sales-transactions.create'))->name('sales-transactions.index');
    Route::get('/sales-transactions/create', [\App\Http\Controllers\SalesTransactionController::class, 'create'])->name('sales-transactions.create');
    Route::post('/sales-transactions', [\App\Http\Controllers\SalesTransactionController::class, 'store'])->name('sales-transactions.store');
    Route::post('/sales-transactions/{salesTransaction}/mark-as-paid', [\App\Http\Controllers\SalesTransactionController::class, 'markAsPaid'])->name('sales-transactions.mark-as-paid');

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

    Route::get('/payments', [\App\Http\Controllers\AccountPayable\PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [\App\Http\Controllers\AccountPayable\PaymentController::class, 'store'])->name('payments.store');

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
    Route::patch('/procurement/matching/{bill}/resolve', [MatchingController::class, 'resolve'])->name('procurement.matching.resolve');
});
