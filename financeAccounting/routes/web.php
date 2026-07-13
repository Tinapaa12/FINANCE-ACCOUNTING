<?php // web.php — defines all web routes for the application. Includes General Ledger, Reports, Tax Compliance, Accounts Receivable, and Dashboard.
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeneralLedger\ChartOfAccountsController;
use App\Http\Controllers\GeneralLedger\JournalEntryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialReporting\FinancialReportController;
use App\Http\Controllers\FinancialReporting\TaxComplianceController;
use App\Http\Controllers\ARController;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/chart-of-accounts/pdf', [ChartOfAccountsController::class, 'pdf'])->name('chart-of-accounts.pdf');
Route::resource('chart-of-accounts', ChartOfAccountsController::class)->parameters([
    'chart-of-accounts' => 'chartOfAccount'
]);

Route::get('/journal-entries/pdf', [JournalEntryController::class, 'pdf'])->name('journal-entries.pdf');
Route::resource('journal-entries', JournalEntryController::class)->parameters([
    'journal-entries' => 'journalEntry'
]);

// Financial Reports pages
Route::get('/reports/income', [FinancialReportController::class, 'income'])->name('reports.income');
Route::get('/reports/assets', [FinancialReportController::class, 'assets'])->name('reports.assets');
Route::get('/reports/liabilities', [FinancialReportController::class, 'liabilities'])->name('reports.liabilities');
Route::get('/reports/cashflow', [FinancialReportController::class, 'cashflow'])->name('reports.cashflow');

// Financial Reports — mock PDF views
Route::get('/reports/income/pdf', [FinancialReportController::class, 'incomePdf'])->name('reports.income.pdf');
Route::get('/reports/assets/pdf', [FinancialReportController::class, 'assetsPdf'])->name('reports.assets.pdf');
Route::get('/reports/liabilities/pdf', [FinancialReportController::class, 'liabilitiesPdf'])->name('reports.liabilities.pdf');
Route::get('/reports/cashflow/pdf', [FinancialReportController::class, 'cashflowPdf'])->name('reports.cashflow.pdf');

// Tax and Compliance
Route::get('/tax-compliance', [TaxComplianceController::class, 'index'])->name('tax.compliance');
Route::get('/tax-compliance/pdf', [TaxComplianceController::class, 'pdf'])->name('tax.compliance.pdf');

// Accounts Receivable
Route::get('/ar/overview', [ARController::class, 'overview'])->name('ar.overview');
Route::get('/ar/payments-received', [ARController::class, 'payments'])->name('ar.payments');
Route::get('/ar/aging-report', [ARController::class, 'aging'])->name('ar.aging');

// Dummy Sales Transactions (simulates an external ERP Sales module)
Route::get('/sales-transactions', fn() => redirect()->route('sales-transactions.create'))->name('sales-transactions.index');
Route::get('/sales-transactions/create', [\App\Http\Controllers\SalesTransactionController::class, 'create'])->name('sales-transactions.create');
Route::post('/sales-transactions', [\App\Http\Controllers\SalesTransactionController::class, 'store'])->name('sales-transactions.store');
Route::post('/sales-transactions/{salesTransaction}/mark-as-paid', [\App\Http\Controllers\SalesTransactionController::class, 'markAsPaid'])->name('sales-transactions.mark-as-paid');

