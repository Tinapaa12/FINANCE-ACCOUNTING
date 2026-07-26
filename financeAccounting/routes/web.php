<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

require __DIR__ . '/auth.php';

Route::middleware('app.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    require __DIR__ . '/general-ledger.php';
    require __DIR__ . '/accounts-payable.php';
    require __DIR__ . '/accounts-receivable.php';
    require __DIR__ . '/procurement.php';
    require __DIR__ . '/financial-reports.php';

    Route::get('/api-playground', function () {
        return view('api-playground', [
            'accounts' => \App\Models\GeneralLedger\ChartOfAccount::where('status', 'Active')->orderBy('account_code')->get(),
            'periods'  => \App\Models\FinancialReporting\BudgetVsActual::select('report_period_start')
                ->get()->map(fn ($b) => \Carbon\Carbon::parse($b->report_period_start)->format('F Y'))
                ->unique()->sort()->values(),
        ]);
    })->name('api-playground');
});
