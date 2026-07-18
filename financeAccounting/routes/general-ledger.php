<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeneralLedger\ChartOfAccountsController;
use App\Http\Controllers\GeneralLedger\JournalEntryController;

Route::prefix('chart-of-accounts')->name('chart-of-accounts.')->group(function () {
    Route::get('/pdf', [ChartOfAccountsController::class, 'pdf'])->name('pdf');
    Route::get('/', [ChartOfAccountsController::class, 'index'])->name('index');
    Route::post('/', [ChartOfAccountsController::class, 'store'])->name('store');
    Route::put('/{chartOfAccount}', [ChartOfAccountsController::class, 'update'])->name('update');
    Route::delete('/{chartOfAccount}', [ChartOfAccountsController::class, 'destroy'])->name('destroy');
});

Route::prefix('journal-entries')->name('journal-entries.')->group(function () {
    Route::get('/pdf', [JournalEntryController::class, 'pdf'])->name('pdf');
    Route::get('/', [JournalEntryController::class, 'index'])->name('index');
    Route::post('/', [JournalEntryController::class, 'store'])->name('store');
    Route::put('/{journalEntry}', [JournalEntryController::class, 'update'])->name('update');
    Route::delete('/{journalEntry}', [JournalEntryController::class, 'destroy'])->name('destroy');
});
