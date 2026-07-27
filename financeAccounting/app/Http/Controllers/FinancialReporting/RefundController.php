<?php
namespace App\Http\Controllers\FinancialReporting;

use App\Http\Controllers\Controller;
use App\Models\AccountPayable\SupplierBill;
use App\Models\Sales\SalesTransaction;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function index()
    {
        $transactions = SalesTransaction::where('status', 'Paid')
            ->orderBy('order_no')
            ->get(['sales_transaction_id', 'order_no', 'customer_name', 'total_amount', 'payment_method']);

        return view('financial-reporting.refund.index', compact('transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sales_transaction_id' => 'required|exists:sales_transactions,sales_transaction_id',
            'refund_request_id'    => 'required|string|max:255',
            'refund_amount'        => 'required|numeric|min:0',
            'refund_status'        => 'required|string|max:255',
            'refund_method'        => 'required|string|max:255',
        ]);

        $transaction = SalesTransaction::find($request->sales_transaction_id);

        SupplierBill::create([
            'bill_no'         => $request->refund_request_id,
            'po_no'           => $transaction->order_no,
            'grn_no'          => '',
            'supplier'        => $transaction->customer_name,
            'amount'          => $request->refund_amount,
            'total_paid'      => 0,
            'due_date'        => now(),
            'status'          => $request->refund_status,
            'payment_method'  => $request->refund_method,
            'matching_status' => 'Matched',
        ]);

        return redirect()->route('sales-refund.index')
            ->with('success', "Refund request {$request->refund_request_id} for {$transaction->customer_name} submitted successfully.");
    }
}
