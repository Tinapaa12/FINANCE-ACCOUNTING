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

// NOTE: 'dashboard' route is already defined elsewhere in your web.php
// via DashboardController — do NOT add a duplicate here, Laravel will
// error on a repeated route name.

// Accounts Receivable
Route::get('/ar/overview', [ARController::class, 'overview'])->name('ar.overview');
Route::get('/ar/payments-received', [ARController::class, 'payments'])->name('ar.payments');
Route::get('/ar/aging-report', [ARController::class, 'aging'])->name('ar.aging');
Route::post('/ar/invoices', [ARController::class, 'store'])->name('ar.invoices.store');
Route::get('/ar/invoices/recent', [ARController::class, 'recentInvoices'])->name('ar.invoices.recent');
Route::post('/ar/payments', [ARController::class, 'storePayment'])->name('ar.payments.store');