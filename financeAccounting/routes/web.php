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
});
