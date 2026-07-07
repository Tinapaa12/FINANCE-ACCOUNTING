<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartOfAccountsController;
use App\Http\Controllers\JournalEntryController;

Route::get('/', function () {
    return redirect()->route('chart-of-accounts.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/chart-of-accounts', [ChartOfAccountsController::class, 'index'])->name('chart-of-accounts.index');
Route::get('/journal-entries', [JournalEntryController::class, 'index'])->name('journal-entries.index');