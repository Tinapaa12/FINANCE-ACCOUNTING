<?php
// Add this to routes/web.php

use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\TaxComplianceController;
use App\Http\Controllers\ARController;

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

// The layout also references a 'dashboard' named route for the sidebar Dashboard link.
// If you already have one (e.g. from Laravel Breeze/Jetstream), leave it as is.
// Otherwise add a placeholder so the link doesn't error:
Route::get('/dashboard', function () {
    return 'Dashboard placeholder';
})->name('dashboard');

// Accounts Receivable
Route::get('/ar/overview', [ARController::class, 'overview'])->name('ar.overview');
Route::get('/ar/payments-received', [ARController::class, 'payments'])->name('ar.payments');
Route::get('/ar/aging-report', [ARController::class, 'aging'])->name('ar.aging');