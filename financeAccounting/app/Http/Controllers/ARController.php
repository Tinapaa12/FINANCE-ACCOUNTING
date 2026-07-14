<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ARController extends Controller
{
    public function overview()
    {
        $customers = Customer::orderBy('name')->get();

        $recentActivities = Invoice::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($inv) {
                return (object)[
                    'id' => $inv->id,
                    'type' => $inv->type === 'credit_note' ? 'Credit Note' : 'Invoice',
                    'ref' => $inv->invoice_number,
                    'customer' => $inv->customer->name,
                    'amount' => (float) $inv->total,
                    'date' => $inv->invoice_date->format('M d'),
                    'status' => ucfirst($inv->status),
                ];
            });

        $recentPayments = Payment::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($p) {
                return (object)[
                    'id' => 'p' . $p->id,
                    'type' => 'Payment',
                    'ref' => $p->reference_no,
                    'customer' => $p->customer->name,
                    'amount' => (float) $p->amount,
                    'date' => $p->payment_date->format('M d'),
                    'status' => ucfirst($p->status),
                ];
            });

        $activities = $recentActivities->concat($recentPayments)->sortByDesc('date')->take(10)->values();

        $totalOutstanding = Invoice::whereIn('status', ['sent', 'overdue'])->sum('total');
        $overdueAmount = Invoice::where('status', 'overdue')->sum('total');
        $overdueCount = Invoice::where('status', 'overdue')->count();

        $collectedThisMonth = Payment::where('status', 'cleared')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');
        $paymentCount = Payment::where('status', 'cleared')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->count();

        $totalInvoices = Invoice::count();

        $agingBuckets = $this->agingBuckets();

        return view('ar.overview', compact(
            'customers', 'activities', 'totalOutstanding', 'overdueAmount', 'overdueCount',
            'collectedThisMonth', 'paymentCount', 'totalInvoices', 'agingBuckets'
        ));
    }

    public function payments()
    {
        $customers = Customer::orderBy('name')->get();
        $invoices = Invoice::with('customer')->whereIn('status', ['draft', 'sent', 'overdue'])->orderBy('invoice_number')->get();

        $payments = Payment::with(['customer', 'applications.invoice'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($p) {
                $appliedText = $p->applications->map(fn($a) => $a->invoice->invoice_number . ' (Full)')->implode(', ');
                return (object)[
                    'id' => $p->id,
                    'ref' => $p->reference_no,
                    'customer' => $p->customer->name,
                    'date' => $p->payment_date->format('M d'),
                    'method' => ucwords(str_replace('_', ' ', $p->method)),
                    'method_raw' => $p->method,
                    'amount' => (float) $p->amount,
                    'applied' => $appliedText ?: 'N/A',
                    'status' => ucfirst($p->status),
                ];
            });

        $collectedThisMonth = Payment::where('status', 'cleared')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');
        $collectedCount = Payment::where('status', 'cleared')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->count();

        $clearedCount = Payment::where('status', 'cleared')->count();
        $pendingCount = Payment::where('status', 'pending')->count();
        $pendingAmount = Payment::where('status', 'pending')->sum('amount');
        $pendingCustomer = Payment::where('status', 'pending')->with('customer')->first();

        $methodTotals = Payment::selectRaw("method, SUM(amount) as total")
            ->groupBy('method')
            ->orderByDesc('total')
            ->get();

        $totalReceived = Payment::sum('amount');
        $topMethod = $methodTotals->first()?->method;
        $topMethodLabel = $topMethod ? ucwords(str_replace('_', ' ', $topMethod)) : 'N/A';

        $methodColors = [
            'bank_transfer' => '#ef4444',
            'gcash' => '#3b82f6',
            'check' => '#22c55e',
            'cash' => '#f59e0b',
        ];
        $methodIcons = [
            'bank_transfer' => 'fa-university',
            'gcash' => 'fa-mobile-screen',
            'check' => 'fa-money-check',
            'cash' => 'fa-money-bill-wave',
        ];

        $methodBreakdown = $methodTotals->map(function ($m) use ($totalReceived, $methodColors, $methodIcons) {
            return (object)[
                'label' => ucwords(str_replace('_', ' ', $m->method)),
                'amount' => (float) $m->total,
                'pct' => $totalReceived > 0 ? round(($m->total / $totalReceived) * 100) : 0,
                'color' => $methodColors[$m->method] ?? '#6b7280',
                'icon' => $methodIcons[$m->method] ?? 'fa-credit-card',
            ];
        });

        return view('ar.payments', compact(
            'customers', 'invoices', 'payments',
            'collectedThisMonth', 'collectedCount',
            'clearedCount', 'pendingCount', 'pendingAmount', 'pendingCustomer',
            'topMethodLabel', 'totalReceived', 'methodBreakdown'
        ));
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'method' => 'required|in:bank_transfer,gcash,check,cash',
            'notes' => 'nullable|string|max:500',
        ]);

        $lastRef = Payment::max('reference_no');
        $nextNum = $lastRef ? (int) substr($lastRef, -4) + 1 : 1;
        $refNo = 'REC-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'customer_id' => $validated['customer_id'],
                'reference_no' => $refNo,
                'payment_date' => $validated['payment_date'],
                'method' => $validated['method'],
                'amount' => $validated['amount'],
                'notes' => $validated['notes'],
                'status' => 'pending',
            ]);

            $payment->applications()->create([
                'invoice_id' => $validated['invoice_id'],
                'amount_applied' => $validated['amount'],
            ]);

            DB::commit();
            return response()->json(['success' => true, 'payment' => $payment->load('customer', 'applications.invoice')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function aging()
    {
        $now = now();
        $customers = Customer::with(['invoices' => function ($q) {
            $q->whereIn('status', ['sent', 'overdue']);
        }])->orderBy('name')->get();

        $agingBuckets = ['current', 'd1_30', 'd31_60', 'd61_90'];
        $bucketRanges = [
            'current' => [-9999, 0],
            'd1_30' => [1, 30],
            'd31_60' => [31, 60],
            'd61_90' => [61, 9999],
        ];
        $bucketLabels = [
            'current' => 'Current (Not Due)',
            'd1_30' => '1 - 30 Days Overdue',
            'd31_60' => '31 - 60 Days Overdue',
            'd61_90' => '61+ Days Overdue',
        ];
        $bucketColors = [
            'current' => '#22c55e',
            'd1_30' => '#fca5a5',
            'd31_60' => '#f87171',
            'd61_90' => '#ef4444',
        ];

        $today = $now->toDateString();

        $customerRows = [];
        $totals = array_fill_keys($agingBuckets, 0);
        $totalOverall = 0;
        $invoiceCounts = array_fill_keys($agingBuckets, 0);

        foreach ($customers as $customer) {
            $row = [
                'customer' => $customer->name,
                'email' => $customer->email,
                'current' => 0,
                'd1_30' => 0,
                'd31_60' => 0,
                'd61_90' => 0,
            ];

            foreach ($customer->invoices as $invoice) {
                $daysOverdue = $now->diffInDays($invoice->due_date, false);
                if ($daysOverdue < 0) $daysOverdue = 0;

                foreach ($agingBuckets as $bucket) {
                    [$min, $max] = $bucketRanges[$bucket];
                    if ($daysOverdue >= $min && $daysOverdue <= $max) {
                        $amount = (float) $invoice->total;
                        $row[$bucket] += $amount;
                        $totals[$bucket] += $amount;
                        $totalOverall += $amount;
                        $invoiceCounts[$bucket]++;
                        break;
                    }
                }
            }

            if (array_sum(array_slice($row, 2)) > 0) {
                $maxBucket = 0;
                foreach (['d61_90', 'd31_60', 'd1_30'] as $b) {
                    if ($row[$b] > 0) { $maxBucket = $b; break; }
                }
                $risk = match ($maxBucket) {
                    'd1_30' => 'Medium',
                    'd31_60' => 'High',
                    'd61_90' => 'Critical',
                    default => 'Low',
                };
                $row['risk'] = $risk;
                $customerRows[] = (object) $row;
            }
        }

        $summaryCards = [];
        foreach ($agingBuckets as $bucket) {
            $summaryCards[] = (object)[
                'label' => $bucketLabels[$bucket],
                'total' => $totals[$bucket],
                'count' => $invoiceCounts[$bucket],
                'pct' => $totalOverall > 0 ? round(($totals[$bucket] / $totalOverall) * 100) : 0,
                'color' => $bucketColors[$bucket],
            ];
        }

        return view('ar.aging', compact('customerRows', 'summaryCards', 'totals', 'totalOverall'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'type' => 'required|in:invoice,credit_note',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'currency' => 'required|string|max:3',
            'subtotal' => 'required|numeric|min:0',
            'vat_amount' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.vat_percent' => 'required|numeric|min:0',
            'items.*.line_total' => 'required|numeric|min:0',
        ]);

        $lastNumber = Invoice::max('invoice_number');
        $nextNumber = $lastNumber ? 'INV-' . str_pad((int) substr($lastNumber, 4) + 1, 4, '0', STR_PAD_LEFT) : 'INV-0001';

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'customer_id' => $validated['customer_id'],
                'invoice_number' => $nextNumber,
                'type' => $validated['type'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'currency' => $validated['currency'],
                'subtotal' => $validated['subtotal'],
                'vat_amount' => $validated['vat_amount'],
                'total' => $validated['total'],
                'status' => 'draft',
            ]);

            foreach ($validated['items'] as $item) {
                $invoice->items()->create($item);
            }

            DB::commit();
            return response()->json(['success' => true, 'invoice' => $invoice->load('customer', 'items')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function recentInvoices()
    {
        return Invoice::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    private function agingBuckets()
    {
        $now = now()->toDateString();
        $buckets = [
            ['label' => 'Current', 'min' => -9999, 'max' => 0, 'color' => '#22c55e'],
            ['label' => '1-30 Days', 'min' => 1, 'max' => 30, 'color' => '#fca5a5'],
            ['label' => '31-60 Days', 'min' => 31, 'max' => 60, 'color' => '#f87171'],
            ['label' => '61-90 Days', 'min' => 61, 'max' => 90, 'color' => '#ef4444'],
            ['label' => '90+ Days', 'min' => 91, 'max' => 9999, 'color' => '#b91c1c'],
        ];

        $result = [];
        foreach ($buckets as $bucket) {
            $amount = Invoice::whereIn('status', ['sent', 'overdue'])
                ->whereRaw("DATEDIFF('$now', due_date) BETWEEN {$bucket['min']} AND {$bucket['max']}")
                ->sum('total');

            $result[] = (object)[
                'label' => $bucket['label'],
                'amount' => (float) ($amount ?: 0),
                'color' => $bucket['color'],
            ];
        }

        return $result;
    }
}
