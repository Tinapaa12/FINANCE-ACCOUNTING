<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeneralLedger\ChartOfAccountsController;
use App\Http\Controllers\GeneralLedger\JournalEntryController;

Route::name('api.')->apiResource('chart-of-accounts', ChartOfAccountsController::class)->parameters(['chart-of-accounts' => 'chartOfAccount']);
Route::name('api.')->apiResource('journal-entries', JournalEntryController::class)->parameters(['journal-entries' => 'journalEntry']);
