<?php // ARController — serves Accounts Receivable overview, payments received, and aging report views.
namespace App\Http\Controllers;

use App\Models\Sales\SalesTransaction;
use Illuminate\Support\Facades\DB;

class ARController extends Controller
{
    public function overview()
    {
        return view('ar.overview');
    }

    public function payments()
    {
        $transactions = SalesTransaction::with('journalEntry')
            ->latest()
            ->get();

        $methodColors = [
            'Cash'          => '#10b981',
            'Credit Card'   => '#3b82f6',
            'Bank Transfer' => '#ef4444',
            'Installment'   => '#f59e0b',
        ];

        $methodTotals = $transactions->groupBy('payment_method')->map(function ($items, $method) use ($methodColors) {
            return [
                'label'  => $method,
                'amount' => $items->sum('total_amount'),
                'color'  => $methodColors[$method] ?? '#6b7280',
            ];
        })->values();

        $grandTotal = $methodTotals->sum('amount');

        $methodBreakdown = $methodTotals->map(function ($item) use ($grandTotal) {
            $item['pct'] = $grandTotal > 0 ? round(($item['amount'] / $grandTotal) * 100) : 0;
            return $item;
        });

        $monthlyTransactions = $transactions->filter(function ($txn) {
            return $txn->created_at && $txn->created_at->isCurrentMonth();
        });
        $monthlyTotal  = $monthlyTransactions->sum('total_amount');
        $monthlyCount  = $monthlyTransactions->count();
        $clearedCount  = $transactions->where('status', 'Paid')->count();
        $pendingAmount = $transactions->where('status', 'Pending')->sum('total_amount');
        $pendingCustomer = $transactions->where('status', 'Pending')->first()?->customer_name;
        $topMethod = $methodTotals->sortByDesc('amount')->first();

        return view('ar.payments', compact(
            'transactions', 'methodBreakdown', 'grandTotal',
            'monthlyTotal', 'monthlyCount', 'clearedCount',
            'pendingAmount', 'pendingCustomer', 'topMethod'
        ));
    }

    public function aging()
    {
        return view('ar.aging');
    }
}
