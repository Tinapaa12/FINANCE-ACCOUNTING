<?php // SalesTransactionController — CRUD for the dummy Sales module. Creates sales transactions and auto-posts Paid transactions to Finance via FinancePostingService.
namespace App\Http\Controllers;

use App\Models\Sales\SalesTransaction;
use App\Services\FinancePostingService;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesTransactionController extends Controller
{
    public function create()
    {
        return view('sales-transactions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'total_amount'   => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,Credit Card,Bank Transfer,Installment',
            'status'         => 'required|in:Pending,Paid',
        ]);

        $year = now()->format('Y');
        $last = SalesTransaction::where('order_no', 'like', "ORD-{$year}-%")
            ->orderBy('sales_transaction_id', 'desc')
            ->first();
        $nextNum = $last ? ((int) (explode('-', $last->order_no)[2] ?? '0')) + 1 : 1;
        $validated['order_no'] = 'ORD-' . $year . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        try {
            $transaction = DB::transaction(function () use ($validated) {
                $transaction = SalesTransaction::create($validated);
                if ($transaction->status === 'Paid') {
                    FinancePostingService::postSale($transaction);
                }
                return $transaction;
            });
        } catch (\Exception $e) {
            return redirect()->route('sales-transactions.create')
                ->with('error', 'Transaction creation failed: ' . $e->getMessage());
        }

        return redirect()->route('sales-transactions.create')
            ->with('success', 'Sales transaction ' . $transaction->order_no . ' created successfully.');
    }

    public function markAsPaid(Request $request, SalesTransaction $salesTransaction)
    {
        if ($salesTransaction->status === 'Paid') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Transaction is already Paid.'], 400);
            }
            return redirect()->back()->with('error', 'Transaction is already Paid.');
        }

        try {
            DB::transaction(function () use ($salesTransaction) {
                $salesTransaction->update(['status' => 'Paid']);
                FinancePostingService::postSale($salesTransaction);

                $customer = Customer::firstOrCreate(['name' => $salesTransaction->customer_name]);
                $year = now()->format('Y');
                $last = Invoice::where('invoice_number', 'like', "INV-{$year}-%")
                    ->orderBy('id', 'desc')
                    ->first();
                $nextNum = $last ? ((int) (explode('-', $last->invoice_number)[2] ?? '0')) + 1 : 1;
                $invoiceNumber = 'INV-' . $year . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
                Invoice::create([
                    'customer_id'    => $customer->id,
                    'invoice_number' => $invoiceNumber,
                    'type'           => 'invoice',
                    'invoice_date'   => now(),
                    'due_date'       => now(),
                    'currency'       => 'PHP',
                    'subtotal'       => $salesTransaction->total_amount,
                    'vat_amount'     => 0,
                    'total'          => $salesTransaction->total_amount,
                    'status'         => 'cleared',
                    'notes'          => json_encode([['desc' => 'Sales - ' . $salesTransaction->order_no, 'qty' => 1, 'price' => $salesTransaction->total_amount]]),
                ]);
            });
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Posting to Finance failed: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Transaction ' . $salesTransaction->order_no . ' marked as Paid.']);
        }

        return redirect()->back()->with('success', 'Transaction ' . $salesTransaction->order_no . ' marked as Paid and posted to Finance.');
    }
}
