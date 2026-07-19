<?php
namespace App\Http\Controllers;

use App\Models\Sales\SalesTransaction;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;

class ARController extends Controller
{
    public function overview()
    {
        $invoices = Invoice::with('customer')->whereIn('type', ['invoice', 'credit_note'])->get();
        $payments = SalesTransaction::where('status', 'Paid')->get();

        $totalOutstanding = $invoices->sum('total');
        $overdueAmount = $invoices->filter(fn($i) => $this->daysOverdue($i) > 0)->sum('total');
        $collectedThisMonth = SalesTransaction::where('status', 'Paid')
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        $recentInvoiceActivities = Invoice::with('customer')->orderBy('id', 'desc')->take(5)->get()->map(function ($inv) {
            $type = $inv->type === 'credit_note' ? 'Credit Note' : 'Invoice';
            if ($inv->status === 'overdue') $type = 'Overdue';
            return [
                'id'       => $inv->id,
                'type'     => $type,
                'ref'      => $inv->invoice_number,
                'customer' => $inv->customer?->name ?? 'Unknown',
                'amount'   => (float) $inv->total,
                'date'     => $inv->invoice_date?->format('M d') ?? $inv->created_at->format('M d'),
                'status'   => ucfirst($inv->status),
                '_sort'    => $inv->created_at?->timestamp ?? 0,
            ];
        });

        $recentPaymentActivities = SalesTransaction::where('status', 'Paid')
            ->latest()->take(5)->get()->map(function ($t) {
                return [
                    'id'       => $t->sales_transaction_id,
                    'type'     => 'Payment',
                    'ref'      => $t->order_no,
                    'customer' => $t->customer_name,
                    'amount'   => (float) $t->total_amount,
                    'date'     => $t->created_at->format('M d'),
                    'status'   => $t->status,
                    '_sort'    => $t->created_at?->timestamp ?? 0,
                ];
            });

        $recentActivities = $recentInvoiceActivities->concat($recentPaymentActivities)
            ->sortByDesc('_sort')
            ->take(5)
            ->values()
            ->map(fn($item) => collect($item)->except('_sort')->all());

        $agingBuckets = $this->computeAgingBuckets($invoices);

        $sidebarInvoices = Invoice::with('customer')
            ->whereIn('status', ['draft', 'sent', 'overdue'])
            ->orderBy('id', 'desc')
            ->take(4)
            ->get()
            ->map(fn($i) => [
                'order_no' => $i->invoice_number,
                'customer' => $i->customer?->name ?? 'Unknown',
                'amount'   => (float) $i->total,
                'status'   => ucfirst($i->status),
            ]);

        $invoiceCount = $invoices->count();
        $overdueCount = $invoices->filter(fn($i) => $this->daysOverdue($i) > 0)->count();
        $paymentCount = $payments->count();

        $avgDaysToCollect = Invoice::where('status', 'cleared')
            ->whereNotNull('invoice_date')
            ->whereNotNull('updated_at')
            ->get()
            ->filter(fn($i) => $i->invoice_date)
            ->avg(fn($i) => (int) $i->invoice_date->diffInDays($i->updated_at));

        $avgDaysToCollect = $avgDaysToCollect ? round($avgDaysToCollect) : 0;

        return view('ar.overview', compact(
            'totalOutstanding', 'overdueAmount', 'collectedThisMonth',
            'recentActivities', 'agingBuckets', 'sidebarInvoices',
            'invoiceCount', 'overdueCount', 'paymentCount', 'avgDaysToCollect'
        ));
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
        $pendingAmount = $transactions->where('status', 'Sent')->sum('total_amount');
        $pendingCustomer = $transactions->where('status', 'Sent')->first()?->customer_name;
        $topMethod = $methodTotals->sortByDesc('amount')->first();

        return view('ar.payments', compact(
            'transactions', 'methodBreakdown', 'grandTotal',
            'monthlyTotal', 'monthlyCount', 'clearedCount',
            'pendingAmount', 'pendingCustomer', 'topMethod'
        ));
    }

    public function aging()
    {
        $invoices = Invoice::with('customer')->whereIn('status', ['sent', 'overdue'])->get();

        $currentAmount  = $invoices->filter(fn($i) => $i->status === 'sent' && $i->due_date && $i->due_date->isFuture())->sum('total');
        $d1_30Amount    = $invoices->filter(fn($i) => $this->daysOverdue($i) >= 1 && $this->daysOverdue($i) <= 30)->sum('total');
        $d31_60Amount   = $invoices->filter(fn($i) => $this->daysOverdue($i) >= 31 && $this->daysOverdue($i) <= 60)->sum('total');
        $d61_90Amount   = $invoices->filter(fn($i) => $this->daysOverdue($i) >= 61 && $this->daysOverdue($i) <= 90)->sum('total');
        $d90Amount      = $invoices->filter(fn($i) => $this->daysOverdue($i) >= 91)->sum('total');

        $currentCount  = $invoices->filter(fn($i) => $i->status === 'sent' && $i->due_date && $i->due_date->isFuture())->count();
        $d1_30Count    = $invoices->filter(fn($i) => $this->daysOverdue($i) >= 1 && $this->daysOverdue($i) <= 30)->count();
        $d31_60Count   = $invoices->filter(fn($i) => $this->daysOverdue($i) >= 31 && $this->daysOverdue($i) <= 60)->count();
        $d61_90Count   = $invoices->filter(fn($i) => $this->daysOverdue($i) >= 61 && $this->daysOverdue($i) <= 90)->count();
        $d90Count      = $invoices->filter(fn($i) => $this->daysOverdue($i) >= 91)->count();

        $customers = $invoices->groupBy(fn($i) => $i->customer->name ?? 'Unknown')->map(function ($items, $customer) {
            $current = $items->filter(fn($i) => $i->status === 'sent' && $i->due_date && $i->due_date->isFuture())->sum('total');
            $d1_30   = $items->filter(fn($i) => $this->daysOverdue($i) >= 1 && $this->daysOverdue($i) <= 30)->sum('total');
            $d31_60  = $items->filter(fn($i) => $this->daysOverdue($i) >= 31 && $this->daysOverdue($i) <= 60)->sum('total');
            $d61_90  = $items->filter(fn($i) => $this->daysOverdue($i) >= 61 && $this->daysOverdue($i) <= 90)->sum('total');
            $d90     = $items->filter(fn($i) => $this->daysOverdue($i) >= 91)->sum('total');
            $total   = $current + $d1_30 + $d31_60 + $d61_90 + $d90;

            $risk = 'Low';
            if ($d90 > 0 || $d61_90 > 0) $risk = 'High';
            elseif ($d31_60 > 0) $risk = 'Medium';

            return compact('customer', 'current', 'd1_30', 'd31_60', 'd61_90', 'd90', 'total', 'risk');
        })->values();

        $grandCurrent = $customers->sum('current');
        $grandD1_30   = $customers->sum('d1_30');
        $grandD31_60  = $customers->sum('d31_60');
        $grandD61_90  = $customers->sum('d61_90');
        $grandD90     = $customers->sum('d90');
        $grandTotal   = $customers->sum('total');

        $totalPct = $grandCurrent + $grandD1_30 + $grandD31_60 + $grandD61_90 + $grandD90;
        $pctCurrent = $totalPct > 0 ? round(($grandCurrent / $totalPct) * 100) : 0;
        $pct1_30    = $totalPct > 0 ? round(($grandD1_30 / $totalPct) * 100) : 0;
        $pct31_60   = $totalPct > 0 ? round(($grandD31_60 / $totalPct) * 100) : 0;
        $pct61_90   = $totalPct > 0 ? round(($grandD61_90 / $totalPct) * 100) : 0;
        $pct90      = $totalPct > 0 ? round(($grandD90 / $totalPct) * 100) : 0;

        return view('ar.aging', compact(
            'currentAmount', 'd1_30Amount', 'd31_60Amount', 'd61_90Amount', 'd90Amount',
            'currentCount', 'd1_30Count', 'd31_60Count', 'd61_90Count', 'd90Count',
            'pctCurrent', 'pct1_30', 'pct31_60', 'pct61_90', 'pct90',
            'customers', 'grandCurrent', 'grandD1_30', 'grandD31_60', 'grandD61_90', 'grandD90', 'grandTotal'
        ));
    }

    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'invoice_type'   => 'required|string|in:Invoice,Credit Note',
            'invoice_date'   => 'required|date',
            'due_date'       => 'required|date|after_or_equal:invoice_date',
            'currency'       => 'required|string|size:3',
            'subtotal'       => 'required|numeric|min:0',
            'vat_amount'     => 'required|numeric|min:0',
            'total_amount'   => 'required|numeric|min:0',
            'line_items'     => 'required|json',
            'status'         => 'required|in:Draft,Sent',
        ]);

        $customer = Customer::firstOrCreate(
            ['name' => $validated['customer_name']]
        );

        $year = now()->format('Y');
        $last = Invoice::where('invoice_number', 'like', "INV-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();
        $nextNum = $last ? (int) substr($last->invoice_number, -3) + 1 : 1;
        $invoiceNumber = 'INV-' . $year . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        Invoice::create([
            'customer_id'    => $customer->id,
            'invoice_number' => $invoiceNumber,
            'type'           => $validated['invoice_type'] === 'Credit Note' ? 'credit_note' : 'invoice',
            'invoice_date'   => $validated['invoice_date'],
            'due_date'       => $validated['due_date'],
            'currency'       => $validated['currency'],
            'subtotal'       => $validated['subtotal'],
            'vat_amount'     => $validated['vat_amount'],
            'total'          => $validated['total_amount'],
            'status'         => strtolower($validated['status']),
            'notes'          => $validated['line_items'],
        ]);

        return redirect()->route('ar.overview')->with('success', 'Invoice ' . $invoiceNumber . ' created successfully.');
    }

    private function daysOverdue($invoice)
    {
        if (!$invoice->due_date) return 0;
        $due = $invoice->due_date;
        if ($due->isFuture()) return 0;
        return (int) $due->diffInDays(now());
    }

    private function computeAgingBuckets($invoices)
    {
        $buckets = [
            ['label' => 'Current',     'amount' => 0, 'color' => '#22c55e'],
            ['label' => '1-30 Days',   'amount' => 0, 'color' => '#fca5a5'],
            ['label' => '31-60 Days',  'amount' => 0, 'color' => '#f87171'],
            ['label' => '61-90 Days',  'amount' => 0, 'color' => '#ef4444'],
            ['label' => '90+ Days',    'amount' => 0, 'color' => '#b91c1c'],
        ];

        foreach ($invoices as $inv) {
            $days = $this->daysOverdue($inv);
            if ($days > 0) {
                if ($days >= 91)      $buckets[4]['amount'] += $inv->total;
                elseif ($days >= 61)  $buckets[3]['amount'] += $inv->total;
                elseif ($days >= 31)  $buckets[2]['amount'] += $inv->total;
                else                  $buckets[1]['amount'] += $inv->total;
            } elseif ($inv->due_date && $inv->due_date->isFuture()) {
                $buckets[0]['amount'] += $inv->total;
            }
        }

        return $buckets;
    }
}
