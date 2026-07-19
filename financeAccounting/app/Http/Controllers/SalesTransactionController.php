<?php // SalesTransactionController — CRUD for the dummy Sales module. Creates sales transactions and auto-posts Paid transactions to Finance via FinancePostingService.
namespace App\Http\Controllers;

use App\Models\Sales\SalesTransaction;
use App\Services\FinancePostingService;
use Illuminate\Http\Request;

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
        $nextNum = $last ? (int) substr($last->order_no, -3) + 1 : 1;
        $validated['order_no'] = 'ORD-' . $year . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        $transaction = SalesTransaction::create($validated);

        if ($transaction->status === 'Paid') {
            try {
                FinancePostingService::postSale($transaction);
            } catch (\Exception $e) {
                return redirect()->route('sales-transactions.create')
                    ->with('error', 'Transaction created but posting to Finance failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('sales-transactions.create')
            ->with('success', 'Sales transaction ' . $transaction->order_no . ' created successfully.');
    }

    public function markAsPaid(Request $request, SalesTransaction $salesTransaction)
    {
        if (in_array($salesTransaction->status, ['Paid', 'Cleared'])) {
            return redirect()->back()->with('error', 'Transaction is already Paid or Cleared.');
        }

        $salesTransaction->update(['status' => 'Paid']);

        try {
            FinancePostingService::postSale($salesTransaction);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Posting to Finance failed: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Transaction ' . $salesTransaction->order_no . ' marked as Paid.']);
        }

        return redirect()->back()->with('success', 'Transaction ' . $salesTransaction->order_no . ' marked as Paid and posted to Finance.');
    }
}
