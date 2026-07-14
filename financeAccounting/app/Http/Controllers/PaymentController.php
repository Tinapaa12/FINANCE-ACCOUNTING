<?php

namespace App\Http\Controllers;

use App\Models\AccountsPayable\SupplierBill;
use App\Models\AccountsPayable\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('supplierBill');

        if ($search = $request->input('search')) {
            $query->whereHas('supplierBill', function ($q) use ($search) {
                $q->where('supplier', 'like', "%{$search}%")
                  ->orWhere('bill_no', 'like', "%{$search}%");
            })->orWhere('payment_method', 'like', "%{$search}%")
              ->orWhere('reference', 'like', "%{$search}%");
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('accounts-payable.payments.index', compact('payments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_bill_id' => 'required|exists:supplier_bills,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string',
            'payment_date' => 'required|date',
            'reference' => 'nullable|string',
        ]);

        $bill = SupplierBill::findOrFail($request->supplier_bill_id);
        $newTotal = $bill->total_paid + $request->amount;

        if ($newTotal > $bill->amount) {
            return back()->withErrors(['amount' => 'Payment exceeds the bill amount of ₱' . number_format($bill->amount, 2)]);
        }

        Payment::create([
            'supplier_bill_id' => $bill->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method ?: $bill->payment_method,
            'payment_date' => $request->payment_date,
            'reference' => $request->reference,
        ]);

        $bill->total_paid = $newTotal;

        if ($newTotal >= $bill->amount) {
            $bill->status = 'Paid';
            $bill->paid_at = now();
        }

        $bill->save();

        audit_log($bill, 'payment', "Payment of ₱{$request->amount} recorded for bill #{$bill->bill_no}", null, ['amount' => $request->amount, 'method' => $request->payment_method, 'reference' => $request->reference]);

        return redirect()->route('supplier-bills.index');
    }
}
