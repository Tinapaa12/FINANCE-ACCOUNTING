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

    // Accounts Receivable
    Route::get('/ar/overview', [ARController::class, 'overview'])->name('ar.overview');
    Route::get('/ar/payments-received', [ARController::class, 'payments'])->name('ar.payments');
    Route::get('/ar/aging-report', [ARController::class, 'aging'])->name('ar.aging');
    Route::post('/ar/invoices', [ARController::class, 'storeInvoice'])->name('ar.invoices.store');

    Route::get('/sales-transactions', fn() => redirect()->route('sales-transactions.create'))->name('sales-transactions.index');
    Route::get('/sales-transactions/create', [\App\Http\Controllers\SalesTransactionController::class, 'create'])->name('sales-transactions.create');
    Route::post('/sales-transactions', [\App\Http\Controllers\SalesTransactionController::class, 'store'])->name('sales-transactions.store');
    Route::post('/sales-transactions/{salesTransaction}/mark-as-paid', [\App\Http\Controllers\SalesTransactionController::class, 'markAsPaid'])->name('sales-transactions.mark-as-paid');
});
