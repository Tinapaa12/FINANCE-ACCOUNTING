<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialReporting\FinancialReportController;
use App\Http\Controllers\FinancialReporting\TaxComplianceController;
use App\Http\Controllers\FinancialReporting\ManageDataController;

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/income', [FinancialReportController::class, 'income'])->name('income');
    Route::get('/assets', [FinancialReportController::class, 'assets'])->name('assets');
    Route::get('/liabilities', [FinancialReportController::class, 'liabilities'])->name('liabilities');
    Route::get('/cashflow', [FinancialReportController::class, 'cashflow'])->name('cashflow');

    Route::get('/income/pdf', [FinancialReportController::class, 'incomePdf'])->name('income.pdf');
    Route::get('/assets/pdf', [FinancialReportController::class, 'assetsPdf'])->name('assets.pdf');
    Route::get('/liabilities/pdf', [FinancialReportController::class, 'liabilitiesPdf'])->name('liabilities.pdf');
    Route::get('/cashflow/pdf', [FinancialReportController::class, 'cashflowPdf'])->name('cashflow.pdf');

    Route::get('/manage', [ManageDataController::class, 'index'])->name('manage');
    Route::post('/manage/store-budget', [ManageDataController::class, 'storeBudget'])->name('manage.store-budget');
    Route::delete('/manage/budget/{budgetVsActual}', [ManageDataController::class, 'destroyBudget'])->name('manage.destroy-budget');
    Route::post('/manage/store-tax', [ManageDataController::class, 'storeTaxRecord'])->name('manage.store-tax');
    Route::delete('/manage/tax/{taxRecord}', [ManageDataController::class, 'destroyTaxRecord'])->name('manage.destroy-tax');
});

Route::prefix('tax-compliance')->name('tax.')->group(function () {
    Route::get('/', [TaxComplianceController::class, 'index'])->name('compliance');
    Route::get('/pdf', [TaxComplianceController::class, 'pdf'])->name('compliance.pdf');
});
