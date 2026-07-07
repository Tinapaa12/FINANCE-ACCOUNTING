<?php
// Add this to routes/web.php

use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\TaxComplianceController;

Route::get('/reports/income', [FinancialReportController::class, 'income'])->name('reports.income');
Route::get('/reports/assets', [FinancialReportController::class, 'assets'])->name('reports.assets');
Route::get('/reports/liabilities', [FinancialReportController::class, 'liabilities'])->name('reports.liabilities');

Route::get('/tax-compliance', [TaxComplianceController::class, 'index'])->name('tax.compliance');

// The layout also references a 'dashboard' named route for the sidebar Dashboard link.
// If you already have one (e.g. from Laravel Breeze/Jetstream), leave it as is.
// Otherwise add a placeholder so the link doesn't error:
Route::get('/dashboard', function () {
    return 'Dashboard placeholder';
})->name('dashboard');

use App\Http\Controllers\ARController;

Route::get('/ar/overview', [ARController::class, 'overview'])->name('ar.overview');
Route::get('/ar/payments-received', [ARController::class, 'payments'])->name('ar.payments');
Route::get('/ar/aging-report', [ARController::class, 'aging'])->name('ar.aging');
