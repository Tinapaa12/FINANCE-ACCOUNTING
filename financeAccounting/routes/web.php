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
use App\Http\Controllers\SupplierBillController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GoodsReceivedNoteController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\AuditController;

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
    Route::post('/reports/manage/store-report', [ManageDataController::class, 'storeReport'])->name('reports.manage.store-report');
    Route::post('/reports/manage/store-income-line', [ManageDataController::class, 'storeIncomeLine'])->name('reports.manage.store-income-line');
    Route::post('/reports/manage/store-trial', [ManageDataController::class, 'storeTrialBalance'])->name('reports.manage.store-trial');
    Route::post('/reports/manage/store-balance', [ManageDataController::class, 'storeBalanceSheet'])->name('reports.manage.store-balance');
    Route::post('/reports/manage/store-cashflow', [ManageDataController::class, 'storeCashFlow'])->name('reports.manage.store-cashflow');
    Route::post('/reports/manage/store-budget', [ManageDataController::class, 'storeBudget'])->name('reports.manage.store-budget');
    Route::post('/reports/manage/store-tax', [ManageDataController::class, 'storeTaxRecord'])->name('reports.manage.store-tax');

    Route::get('/ar/overview', [ARController::class, 'overview'])->name('ar.overview');
    Route::get('/ar/payments-received', [ARController::class, 'payments'])->name('ar.payments');
    Route::get('/ar/aging-report', [ARController::class, 'aging'])->name('ar.aging');

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

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::put('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('purchase-orders.update');
    Route::delete('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');

    Route::get('/goods-received-notes', [GoodsReceivedNoteController::class, 'index'])->name('goods-received-notes.index');
    Route::post('/goods-received-notes', [GoodsReceivedNoteController::class, 'store'])->name('goods-received-notes.store');
    Route::put('/goods-received-notes/{goodsReceivedNote}', [GoodsReceivedNoteController::class, 'update'])->name('goods-received-notes.update');
    Route::delete('/goods-received-notes/{goodsReceivedNote}', [GoodsReceivedNoteController::class, 'destroy'])->name('goods-received-notes.destroy');

    Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
    Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
    Route::put('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('payment-methods.update');
    Route::delete('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');

    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
});
