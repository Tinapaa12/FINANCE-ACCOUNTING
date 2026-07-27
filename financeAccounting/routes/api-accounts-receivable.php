<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ARController;
use App\Http\Controllers\SalesTransactionController;

Route::get("ar/overview", [ARController::class, "overview"])->name("api.ar.overview");
Route::get("ar/payments-received", [ARController::class, "payments"])->name("api.ar.payments");
Route::get("ar/aging-report", [ARController::class, "aging"])->name("api.ar.aging");
Route::match(["GET", "POST"], "ar/aging-report/remind", [ARController::class, "remindCustomer"])->name("api.ar.aging.remind");
Route::get("ar/detail", [ARController::class, "detail"])->name("api.ar.detail");
Route::get("ar/sales-refund", function () {
    $refunds = App\Models\AccountPayable\SupplierBill::where("bill_no", "like", "REF-%")->orderBy("created_at", "desc")->get()->map(function ($refund) {
        return [
            "id" => $refund->id,
            "bill_no" => $refund->bill_no,
            "po_no" => $refund->po_no,
            "supplier" => $refund->supplier,
            "amount" => $refund->amount,
            "total_paid" => $refund->total_paid,
            "balance" => $refund->balance,
            "due_date" => $refund->due_date,
            "status" => $refund->status,
            "matching_status" => $refund->matching_status,
            "payment_method" => $refund->payment_method,
            "created_at" => $refund->created_at,
            "updated_at" => $refund->updated_at,
        ];
    });
    return response()->json(["success" => true, "data" => $refunds], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
})->name("api.ar.sales-refund");
Route::post("sales-transactions", [SalesTransactionController::class, "store"])->name("api.sales-transactions.store");
Route::post("sales-transactions/{salesTransaction}/mark-as-paid", [SalesTransactionController::class, "markAsPaid"])->name("api.sales-transactions.mark-as-paid");
