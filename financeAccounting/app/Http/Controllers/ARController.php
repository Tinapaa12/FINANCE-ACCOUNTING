<?php
namespace App\Http\Controllers;

use App\Models\Sales\SalesTransaction;
use App\Models\Invoice;
use App\Services\DunningLetterService;
use Illuminate\Http\Request;


class ARController extends Controller
{
    public function overview()
    {
        $invoices = Invoice::with('customer')->whereIn('type', ['invoice', 'credit_note'])->get();
        $payments = SalesTransaction::where('status', 'Paid')->get();

        $totalOutstanding = $invoices->where('type', 'invoice')->sum('total') - $invoices->where('type', 'credit_note')->sum('total');
        $overdueAmount = $invoices->filter(fn($i) => $this->daysOverdue($i) > 0)->sum('total');
        $collectedThisMonth = SalesTransaction::where('status', 'Paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
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

        $invoiceCount = $invoices->count();
        $overdueCount = $invoices->whereIn('status', ['sent', 'overdue'])->filter(fn($i) => $this->daysOverdue($i) > 0)->count();
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
            'recentActivities',
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
            'Pay Later'     => '#f59e0b',
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
        $invoices = Invoice::with('customer')->whereIn('status', ['sent', 'overdue'])->get();

        $payLaterTxns = SalesTransaction::where('payment_method', 'Pay Later')
            ->where('status', 'Pending')
            ->whereNotNull('due_date')
            ->get();

        $agingItems = collect();

        foreach ($invoices as $inv) {
            $agingItems->push((object) [
                'customer_name' => $inv->customer?->name ?? 'Unknown',
                'amount'        => (float) $inv->total,
                'due_date'      => $inv->due_date,
                'status'        => $inv->status,
                'source'        => 'invoice',
            ]);
        }

        foreach ($payLaterTxns as $txn) {
            $remaining = $txn->total_amount - ($txn->initial_payment ?? 0);
            if ($remaining <= 0) continue;
            $agingItems->push((object) [
                'customer_name' => $txn->customer_name,
                'amount'        => $remaining,
                'due_date'      => $txn->due_date,
                'status'        => $txn->status,
                'source'        => 'pay_later',
            ]);
        }

        $currentAmount = $agingItems->filter(fn($i) => (!$i->due_date || $i->due_date->isFuture()))->sum('amount');
        $d1_30Amount   = $agingItems->filter(fn($i) => $this->daysOverdue($i) >= 1 && $this->daysOverdue($i) <= 30)->sum('amount');
        $d31_60Amount  = $agingItems->filter(fn($i) => $this->daysOverdue($i) >= 31 && $this->daysOverdue($i) <= 60)->sum('amount');
        $d61_90Amount  = $agingItems->filter(fn($i) => $this->daysOverdue($i) >= 61 && $this->daysOverdue($i) <= 90)->sum('amount');
        $d90Amount     = $agingItems->filter(fn($i) => $this->daysOverdue($i) >= 91)->sum('amount');

        $currentCount = $agingItems->filter(fn($i) => (!$i->due_date || $i->due_date->isFuture()))->count();
        $d1_30Count   = $agingItems->filter(fn($i) => $this->daysOverdue($i) >= 1 && $this->daysOverdue($i) <= 30)->count();
        $d31_60Count  = $agingItems->filter(fn($i) => $this->daysOverdue($i) >= 31 && $this->daysOverdue($i) <= 60)->count();
        $d61_90Count  = $agingItems->filter(fn($i) => $this->daysOverdue($i) >= 61 && $this->daysOverdue($i) <= 90)->count();
        $d90Count     = $agingItems->filter(fn($i) => $this->daysOverdue($i) >= 91)->count();

        $customers = $agingItems->groupBy(fn($i) => $i->customer_name)->map(function ($items, $customer) {
            $current = $items->filter(fn($i) => (!$i->due_date || $i->due_date->isFuture()))->sum('amount');
            $d1_30   = $items->filter(fn($i) => $this->daysOverdue($i) >= 1 && $this->daysOverdue($i) <= 30)->sum('amount');
            $d31_60  = $items->filter(fn($i) => $this->daysOverdue($i) >= 31 && $this->daysOverdue($i) <= 60)->sum('amount');
            $d61_90  = $items->filter(fn($i) => $this->daysOverdue($i) >= 61 && $this->daysOverdue($i) <= 90)->sum('amount');
            $d90     = $items->filter(fn($i) => $this->daysOverdue($i) >= 91)->sum('amount');
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

    public function customerPay()
    {
        return view('ar.customer-pay');
    }

    public function detail(Request $request)
    {
        $type = $request->query('type');
        $id = $request->query('id');

        if ($type === 'invoice') {
            $invoice = Invoice::with('customer')->find($id);
            if (!$invoice) return response()->json(['success' => false, 'message' => 'Invoice not found'], 404);
            return response()->json(['success' => true, 'data' => [
                'id' => $invoice->id,
                'type' => $invoice->type === 'credit_note' ? 'Credit Note' : 'Invoice',
                'invoice_number' => $invoice->invoice_number,
                'customer' => $invoice->customer?->name ?? 'Unknown',
                'customer_email' => $invoice->customer?->email ?? null,
                'customer_phone' => $invoice->customer?->phone ?? null,
                'invoice_date' => $invoice->invoice_date?->format('M d, Y'),
                'due_date' => $invoice->due_date?->format('M d, Y'),
                'total' => (float) $invoice->total,
                'subtotal' => (float) $invoice->subtotal,
                'vat_amount' => (float) $invoice->vat_amount,
'status' => ucfirst($invoice->status),
            ]], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

if ($type === 'transaction') {
            $txn = SalesTransaction::find($id);
            if (!$txn) return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
            return response()->json(['success' => true, 'data' => [
                'id' => $txn->sales_transaction_id,
                'type' => 'Payment',
                'order_no' => $txn->order_no,
                'customer' => $txn->customer_name,
                'phone' => $txn->phone_number ?? null,
                'total_amount' => (float) $txn->total_amount,
                'initial_payment' => (float) ($txn->initial_payment ?? 0),
                'payment_method' => $txn->payment_method,
                'status' => $txn->status,
                'created_at' => $txn->created_at?->format('M d, Y'),
            ]], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
    }

    public function remindCustomer(Request $request)
    {
        $customer = $request->query('customer')
            ?? $request->input('customer')
            ?? $request->json('customer');

        $removeRef = $request->query('remove')
            ?? $request->input('remove');
        if ($removeRef) {
            $reminded = session()->get('reminded_customers', []);
            $found = null;
            foreach ($reminded as $key => $item) {
                if ($item['ref'] === $removeRef) {
                    $found = $key;
                    break;
                }
            }
            if ($found !== null) {
                unset($reminded[$found]);
                session()->put('reminded_customers', $reminded);
            }
            return response()->json(['success' => true, 'removed' => $removeRef]);
        }

        if ($customer) {
            $payLaterTxns = SalesTransaction::where('payment_method', 'Pay Later')
                ->where('customer_name', $customer)
                ->where('status', 'Pending')
                ->whereNotNull('due_date')
                ->get();

            $overdueInvoices = Invoice::whereHas('customer', fn($q) => $q->where('name', $customer))
                ->whereIn('status', ['sent', 'overdue'])
                ->get();

            $items = collect();
            $sentMessages = [];

            foreach ($payLaterTxns as $txn) {
                $remaining = $txn->total_amount - ($txn->initial_payment ?? 0);
                if ($remaining <= 0) continue;

                $result = DunningLetterService::send([
                    'customer_name' => $txn->customer_name,
                    'total_amount' => (float) $txn->total_amount,
                    'initial_payment' => (float) ($txn->initial_payment ?? 0),
                    'due_date' => $txn->due_date,
                    'reference' => $txn->order_no,
                    'phone' => $txn->phone_number,
                ]);
                $sentMessages[] = $result;

            $items->push([
                'type' => 'Pay Later',
                'customer' => $txn->customer_name,
                'ref' => $txn->order_no,
                'txn_id' => $txn->sales_transaction_id,
                'amount' => $remaining,
                'due_date' => $txn->due_date?->format('Y-m-d'),
                'days_overdue' => $txn->due_date ? max(0, now()->startOfDay()->diffInDays($txn->due_date, false)) : 0,
                'phone' => $txn->phone_number,
                'message_sent' => $result['message'],
            ]);
            }

            foreach ($overdueInvoices as $inv) {
                $customerName = $inv->customer?->name ?? $customer;
                $phone = $inv->customer?->phone ?? $inv->customer?->phone_number ?? null;

                $result = DunningLetterService::send([
                    'customer_name' => $customerName,
                    'total_amount' => (float) $inv->total,
                    'due_date' => $inv->due_date,
                    'reference' => $inv->invoice_number,
                    'phone' => $phone,
                ]);
                $sentMessages[] = $result;

                $items->push([
                    'type' => 'Invoice',
                    'customer' => $customerName,
                    'ref' => $inv->invoice_number,
                    'amount' => (float) $inv->total,
                    'due_date' => $inv->due_date?->format('Y-m-d'),
                    'days_overdue' => $inv->due_date ? max(0, now()->startOfDay()->diffInDays($inv->due_date, false)) : 0,
                    'phone' => $phone,
                    'message_sent' => $result['message'],
                ]);
            }

            $reminded = session()->get('reminded_customers', []);
            foreach ($items as $item) {
                $key = $item['customer'] . '|' . $item['ref'];
                $reminded[$key] = $item;
            }
            session()->put('reminded_customers', $reminded);

            $dunningData = $sentMessages[0] ?? null;
            $totalDue = $items->sum('amount');
            $phoneNumbers = collect($sentMessages)->pluck('phone')->filter()->unique()->values();

            return response()->json([
                'success' => true,
                'customer' => $customer,
                'phone' => $phoneNumbers->first() ?? null,
                'items' => $items,
                'total_due' => $totalDue,
                'item_count' => $items->count(),
                'messages_sent' => count($sentMessages),
                'dunning_letter' => $dunningData,
                'message' => "Dunning letter sent to {$customer}."
                    . ($phoneNumbers->isNotEmpty() ? " Phones: " . $phoneNumbers->implode(', ') : "")
                    . " Total due: ₱" . number_format($totalDue, 2) . " across {$items->count()} item(s).",
            ]);
        }

        return response()->json([
            'success' => true,
            'reminded_customers' => array_values(session()->get('reminded_customers', [])),
            'total_reminded' => count(session()->get('reminded_customers', [])),
        ]);
    }

    private function daysOverdue($item)
    {
        if (!$item->due_date) return 0;
        $due = $item->due_date;
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
            } elseif (!$inv->due_date || $inv->due_date->isFuture()) {
                $buckets[0]['amount'] += $inv->total;
            }
        }

        return $buckets;
    }
}
