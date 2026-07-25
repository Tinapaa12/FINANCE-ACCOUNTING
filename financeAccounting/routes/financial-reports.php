<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialReporting\FinancialReportController;
use App\Http\Controllers\FinancialReporting\TaxComplianceController;

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/income', [FinancialReportController::class, 'income'])->name('income');
    Route::get('/assets', [FinancialReportController::class, 'assets'])->name('assets');
    Route::get('/budget', [FinancialReportController::class, 'budget'])->name('budget');
    Route::get('/cashflow', [FinancialReportController::class, 'cashflow'])->name('cashflow');

    Route::get('/income/pdf', [FinancialReportController::class, 'incomePdf'])->name('income.pdf');
    Route::get('/assets/pdf', [FinancialReportController::class, 'assetsPdf'])->name('assets.pdf');
    Route::get('/budget/pdf', [FinancialReportController::class, 'budgetPdf'])->name('budget.pdf');
    Route::get('/cashflow/pdf', [FinancialReportController::class, 'cashflowPdf'])->name('cashflow.pdf');
});

Route::prefix('tax-compliance')->name('tax.')->group(function () {
    Route::get('/', [TaxComplianceController::class, 'index'])->name('compliance');
    Route::get('/pdf', [TaxComplianceController::class, 'pdf'])->name('compliance.pdf');
});
