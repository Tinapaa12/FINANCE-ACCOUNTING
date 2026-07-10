<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartOfAccountsController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\TaxComplianceController;

Route::get('/', function () {
    return redirect()->route('chart-of-accounts.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/chart-of-accounts', [ChartOfAccountsController::class, 'index'])->name('chart-of-accounts.index');
Route::get('/journal-entries', [JournalEntryController::class, 'index'])->name('journal-entries.index');

Route::get('/reports/income', [FinancialReportController::class, 'income'])->name('reports.income');
Route::get('/reports/assets', [FinancialReportController::class, 'assets'])->name('reports.assets');
Route::get('/reports/liabilities', [FinancialReportController::class, 'liabilities'])->name('reports.liabilities');

Route::get('/tax-compliance', [TaxComplianceController::class, 'index'])->name('tax.compliance');