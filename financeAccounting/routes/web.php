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
        return response()->json(['success' => true, 'data' => $transactions]);
    })->name('sales-transactions.json');
    Route::get('/supply-chain/bills', function () {
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
        return response()->json(['success' => true, 'data' => $bills]);
    })->name('supply-chain.bills');
});
