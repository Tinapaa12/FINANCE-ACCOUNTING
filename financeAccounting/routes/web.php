<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\TransactionCostController;

require __DIR__ . '/auth.php';

Route::middleware('app.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    require __DIR__ . '/general-ledger.php';
    require __DIR__ . '/accounts-payable.php';
    require __DIR__ . '/accounts-receivable.php';
    require __DIR__ . '/procurement.php';
    require __DIR__ . '/financial-reports.php';

    Route::get('/supply-chain/create', [TransactionCostController::class, 'create'])->name('supply-chain.create');
    Route::get('/sales-transactions/json', function () {
        $transactions = App\Models\Sales\SalesTransaction::orderBy('created_at', 'desc')->get()->map(function ($t) {
            return [
                'id' => $t->sales_transaction_id,
                'order_no' => $t->order_no,
                'customer_name' => $t->customer_name,
                'total_amount' => $t->total_amount,
                'payment_method' => $t->payment_method,
                'status' => $t->status,
                'created_at' => $t->created_at,
                'updated_at' => $t->updated_at,
            ];
        });
        return response()->json(['success' => true, 'data' => $transactions], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    })->name('sales-transactions.json');
    Route::get('/supplier-bills/json', function () {
        $bills = App\Models\AccountPayable\SupplierBill::orderBy('created_at', 'desc')->get()->map(function ($bill) {
            return [
                'id' => $bill->id,
                'bill_no' => $bill->bill_no,
                'supplier' => $bill->supplier,
                'amount' => $bill->amount,
                'due_date' => $bill->due_date,
                'status' => $bill->status,
                'matching_status' => $bill->matching_status,
                'created_at' => $bill->created_at,
                'updated_at' => $bill->updated_at,
                'paid_at' => $bill->paid_at,
            ];
        });
        return response()->json(['success' => true, 'data' => $bills], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    })->name('supplier-bills.json');
    Route::get('/chart-of-accounts/json', function () {
        $accounts = App\Models\GeneralLedger\ChartOfAccount::orderBy('account_code')->get();
        return response()->json(['success' => true, 'data' => $accounts], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    })->name('chart-of-accounts.json');
    Route::get('/journal-entries/json', function () {
        $entries = App\Models\GeneralLedger\JournalEntry::with('lines.account')->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $entries], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    })->name('journal-entries.json');
    Route::get('/reports/income/json', function () {
        $c = new App\Http\Controllers\FinancialReporting\FinancialReportController;
        $m = new ReflectionMethod($c, 'incomeData'); $m->setAccessible(true);
        return response()->json(['success' => true, 'data' => $m->invoke($c)], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    });
    Route::get('/reports/assets/json', function () {
        $c = new App\Http\Controllers\FinancialReporting\FinancialReportController;
        $m = new ReflectionMethod($c, 'assetsData'); $m->setAccessible(true);
        return response()->json(['success' => true, 'data' => $m->invoke($c)], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    });
    Route::get('/reports/cashflow/json', function () {
        $c = new App\Http\Controllers\FinancialReporting\FinancialReportController;
        $m = new ReflectionMethod($c, 'cashflowData'); $m->setAccessible(true);
        return response()->json(['success' => true, 'data' => $m->invoke($c)], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    });
    Route::get('/reports/budget/json', function () {
        $c = new App\Http\Controllers\FinancialReporting\FinancialReportController;
        $m = new ReflectionMethod($c, 'budgetData'); $m->setAccessible(true);
        return response()->json(['success' => true, 'data' => $m->invoke($c)], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    });
});
