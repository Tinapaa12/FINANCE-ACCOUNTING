<?php

namespace App\Http\Controllers;

use App\Models\AccountPayable\SupplierBill;
use App\Models\AccountPayable\Payment;
use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\GeneralLedger\JournalEntryLine;
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
        return view('AccountPayable.payments.index', compact('payments'));
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
            $bill->paid_at = $request->payment_date;
            $bill->matching_status = 'Matched';
            $this->createPaymentJournalEntry($bill, $request->reference, $request->payment_date);
        }

        $bill->save();

        audit_log($bill, 'payment', "Payment of ₱{$request->amount} recorded for bill #{$bill->bill_no}", null, ['amount' => $request->amount, 'method' => $request->payment_method, 'reference' => $request->reference]);

        return redirect()->route('supplier-bills.index');
    }

    private function createPaymentJournalEntry(SupplierBill $bill, ?string $reference = null, ?string $paymentDate = null): void
    {
        $apAccount = ChartOfAccount::where('account_code', '2100')->first();
        $cashAccount = ChartOfAccount::where('account_code', '1010')->first();

        if (!$apAccount || !$cashAccount) {
            return;
        }

        $ref = $reference ? 'PO-2026-' . $reference : 'PAY-' . $bill->bill_no . '-' . time();

        $entry = JournalEntry::create([
            'transaction_date' => $paymentDate ?? now(),
            'reference_no' => $ref,
            'description' => "Payment for supplier bill #{$bill->bill_no} - {$bill->supplier}",
            'status' => 'Posted',
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry->journal_entry_id,
            'account_id' => $apAccount->account_id,
            'description' => "Accounts Payable - {$bill->supplier} - Bill #{$bill->bill_no}",
            'debit' => $bill->amount,
            'credit' => 0,
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry->journal_entry_id,
            'account_id' => $cashAccount->account_id,
            'description' => "Cash payment - {$bill->supplier} - Bill #{$bill->bill_no}",
            'debit' => 0,
            'credit' => $bill->amount,
        ]);
    }
}
