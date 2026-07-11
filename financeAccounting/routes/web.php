<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartOfAccountsController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\DashboardController;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('chart-of-accounts', ChartOfAccountsController::class)->parameters([
    'chart-of-accounts' => 'chartOfAccount'
]);

Route::resource('journal-entries', JournalEntryController::class)->parameters([
    'journal-entries' => 'journalEntry'
]);