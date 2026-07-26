<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('app.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    require __DIR__ . '/api-general-ledger.php';
    require __DIR__ . '/api-accounts-payable.php';
    require __DIR__ . '/api-procurement.php';
    require __DIR__ . '/api-accounts-receivable.php';
    require __DIR__ . '/api-financial-reports.php';
});
