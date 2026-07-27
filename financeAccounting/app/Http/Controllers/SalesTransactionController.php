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
            'phone_number'   => 'required|string|regex:/^\+63\d{10}$/',
            'total_amount'   => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,Credit Card,Bank Transfer,Pay Later',
            'status'         => 'required|in:Pending,Paid',
            'initial_payment' => 'nullable|numeric|min:0',
            'due_date'       => 'nullable|date',
        ]);

        if ($validated['payment_method'] === 'Pay Later') {
            $validated['initial_payment'] = $validated['initial_payment'] ?? 0;
            if ($validated['initial_payment'] > $validated['total_amount']) {
                return $request->wantsJson()
                    ? response()->json(['success' => false, 'message' => 'Initial payment cannot exceed total amount.'], 422)
                    : back()->withErrors(['initial_payment' => 'Initial payment cannot exceed total amount.']);
            }
            if (!$request->due_date) {
                return $request->wantsJson()
                    ? response()->json(['success' => false, 'message' => 'Due date is required for Pay Later.'], 422)
                    : back()->withErrors(['due_date' => 'Due date is required for Pay Later.']);
            }
            $validated['status'] = 'Pending';
        }

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
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Transaction creation failed: ' . $e->getMessage()], 500);
            }
            return redirect()->route('sales-transactions.create')
                ->with('error', 'Transaction creation failed: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sales transaction ' . $transaction->order_no . ' created successfully.',
                'data' => $transaction,
            ], 201);
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

        $paymentData = null;

        try {
DB::transaction(function () use ($salesTransaction, &$paymentData) {
                  $salesTransaction->update(['status' => 'Paid']);
                  FinancePostingService::postSale($salesTransaction);

                  if ($salesTransaction->payment_method === 'Pay Later') {
                      Invoice::where('customer_id', $salesTransaction->customer_id)
                          ->whereIn('status', ['sent', 'overdue'])
                          ->update(['status' => 'cleared']);
                  }

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

                if ($salesTransaction->payment_method === 'Pay Later') {
                    $remaining = $salesTransaction->total_amount - ($salesTransaction->initial_payment ?? 0);
                    $paymentData = [
                        'type' => 'payment_received',
                        'customer' => $salesTransaction->customer_name,
                        'phone' => $salesTransaction->phone_number,
                        'order_no' => $salesTransaction->order_no,
                        'total_amount' => (float) $salesTransaction->total_amount,
                        'initial_payment' => (float) ($salesTransaction->initial_payment ?? 0),
                        'remaining_paid' => max(0, $remaining),
                        'message' => "Payment received from {$salesTransaction->customer_name} for {$salesTransaction->order_no}. Remaining balance of ₱" . number_format(max(0, $remaining), 2) . " must be paid ASAP.",
                    ];
                }
            });
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Posting to Finance failed: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            $response = [
                'success' => true,
                'message' => 'Transaction ' . $salesTransaction->order_no . ' marked as Paid.',
            ];
            if ($paymentData) {
                $response['payment_data'] = $paymentData;
            }
            return response()->json($response);
        }

        return redirect()->back()->with('success', 'Transaction ' . $salesTransaction->order_no . ' marked as Paid and posted to Finance.');
    }

}
