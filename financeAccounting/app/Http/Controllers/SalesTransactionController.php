<?php // SalesTransactionController — CRUD for the dummy Sales module. Creates sales transactions and auto-posts Paid transactions to Finance via FinancePostingService.
namespace App\Http\Controllers;

use App\Models\SalesTransaction;
use App\Services\FinancePostingService;
use Illuminate\Http\Request;

class SalesTransactionController extends Controller
{
    public function index()
    {
        $transactions = SalesTransaction::with('journalEntry')
            ->latest()
            ->get();

        return view('sales-transactions.index', compact('transactions'));
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
        $nextNum = $last ? (int) substr($last->order_no, -3) + 1 : 1;
        $validated['order_no'] = 'ORD-' . $year . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        $transaction = SalesTransaction::create($validated);

        if ($transaction->status === 'Paid') {
            try {
                FinancePostingService::postSale($transaction);
            } catch (\Exception $e) {
                return redirect()->route('sales-transactions.index')
                    ->with('error', 'Transaction created but posting to Finance failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('sales-transactions.index')
            ->with('success', 'Sales transaction ' . $transaction->order_no . ' created successfully.');
    }

    public function markAsPaid(SalesTransaction $salesTransaction)
    {
        if ($salesTransaction->status === 'Paid') {
            return redirect()->route('sales-transactions.index')
                ->with('error', 'Transaction is already Paid.');
        }

        $salesTransaction->update(['status' => 'Paid']);

        try {
            FinancePostingService::postSale($salesTransaction);
        } catch (\Exception $e) {
            return redirect()->route('sales-transactions.index')
                ->with('error', 'Posting to Finance failed: ' . $e->getMessage());
        }

        return redirect()->route('sales-transactions.index')
            ->with('success', 'Transaction ' . $salesTransaction->order_no . ' marked as Paid and posted to Finance.');
    }
}
