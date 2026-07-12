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
        return view('ar.payments');
    }

    public function aging()
    {
        return view('ar.aging');
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
