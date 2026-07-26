<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialReporting\FinancialReportController;

Route::get('reports/income', [FinancialReportController::class, 'income'])->name('api.reports.income');
Route::get('reports/assets', [FinancialReportController::class, 'assets'])->name('api.reports.assets');
Route::get('reports/budget', [FinancialReportController::class, 'budget'])->name('api.reports.budget');
Route::get('reports/cashflow', [FinancialReportController::class, 'cashflow'])->name('api.reports.cashflow');
