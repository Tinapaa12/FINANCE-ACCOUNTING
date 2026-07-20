<?php

namespace App\Services;

use App\Models\AccountPayable\GoodsReceivedNote;
use App\Models\AccountPayable\Payment;
use App\Models\AccountPayable\PurchaseOrder;
use App\Models\AccountPayable\SupplierBill;
use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\GeneralLedger\JournalEntryLine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AccountPayableService
{
    public function createPurchaseOrder(array $data): PurchaseOrder
    {
        $nextId = PurchaseOrder::count() + 1;

        return PurchaseOrder::create([
            'po_no' => 'PO-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT),
            'supplier' => $data['supplier'],
            'item_name' => $data['item_name'] ?? null,
            'qty' => $data['qty'] ?? null,
            'unit_cost' => $data['unit_cost'] ?? null,
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null,
            'order_date' => $data['order_date'],
            'expected_delivery' => $data['expected_delivery'] ?? null,
            'status' => $data['status'] ?? 'Draft',
        ]);
    }

    public function updatePurchaseOrder(PurchaseOrder $purchaseOrder, array $data): void
    {
        $purchaseOrder->update($data);
    }

    public function approvePurchaseOrder(int $id): PurchaseOrder
    {
        $po = PurchaseOrder::findOrFail($id);
        $po->update(['status' => 'Approved']);
        return $po;
    }

    public function sendPurchaseOrder(int $id): PurchaseOrder
    {
        $po = PurchaseOrder::findOrFail($id);
        $po->update(['status' => 'Sent', 'sent_at' => now()]);
        \audit_log($po, 'sent', "PO #{$po->po_no} sent to supplier");
        return $po;
    }

    public function confirmPurchaseOrder(int $id): PurchaseOrder
    {
        $po = PurchaseOrder::findOrFail($id);
        $po->update(['status' => 'Confirmed', 'confirmed_at' => now()]);
        \audit_log($po, 'confirmed', "PO #{$po->po_no} confirmed");
        return $po;
    }

    public function deliverPurchaseOrder(int $id): PurchaseOrder
    {
        $po = PurchaseOrder::findOrFail($id);
        $po->update(['status' => 'Delivered', 'delivered_at' => now()]);
        \audit_log($po, 'delivered', "PO #{$po->po_no} marked delivered");
        return $po;
    }

    public function cancelPurchaseOrder(int $id): PurchaseOrder
    {
        $po = PurchaseOrder::findOrFail($id);
        $po->update(['status' => 'Cancelled']);
        \audit_log($po, 'cancelled', "PO #{$po->po_no} cancelled");
        return $po;
    }

    public function createGrn(array $data): GoodsReceivedNote
    {
        $nextId = GoodsReceivedNote::count() + 1;

        $po = null;
        if (!empty($data['purchase_order_id'])) {
            $po = PurchaseOrder::find($data['purchase_order_id']);
        }

        return GoodsReceivedNote::create([
            'grn_no' => 'GRN-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT),
            'purchase_order_id' => $data['purchase_order_id'] ?? null,
            'po_no_ref' => $data['po_no_ref'] ?? ($po->po_no ?? null),
            'supplier_bill_id' => $data['supplier_bill_id'] ?? null,
            'item_name' => $data['item_name'] ?? ($po->item_name ?? null),
            'qty_ordered' => $data['qty_ordered'] ?? ($po->qty ?? $data['qty_received']),
            'qty_received' => $data['qty_received'],
            'supplier' => $data['supplier'] ?? ($po->supplier ?? ''),
            'amount' => $data['amount'] ?? ($po->amount ?? 0),
            'received_date' => $data['received_date'],
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? 'Pending',
        ]);
    }

    public function createBill(array $data): SupplierBill
    {
        $nextId = SupplierBill::count() + 1;

        $bill = SupplierBill::create([
            'bill_no' => 'BILL-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT),
            'po_no' => $data['po_no'],
            'grn_no' => $data['grn_no'],
            'supplier' => $data['supplier'],
            'amount' => $data['amount'],
            'due_date' => $data['due_date'],
            'status' => $data['status'] ?? 'Pending',
        ]);

        $this->createExpenseJournalEntry($bill);
        return $bill;
    }

    public function completeGrn(int $id): GoodsReceivedNote
    {
        $grn = GoodsReceivedNote::findOrFail($id);
        $grn->update(['status' => 'Completed']);

        if ($grn->purchase_order_id) {
            PurchaseOrder::where('id', $grn->purchase_order_id)->update([
                'status' => 'Delivered',
                'delivered_at' => now(),
            ]);

            if (!$grn->supplier_bill_id) {
                $po = PurchaseOrder::find($grn->purchase_order_id);
                $nextId = SupplierBill::count() + 1;
                $bill = SupplierBill::create([
                    'bill_no' => 'BILL-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT),
                    'po_no' => $po->po_no,
                    'grn_no' => $grn->grn_no,
                    'supplier' => $grn->supplier ?: $po->supplier,
                    'amount' => $po->amount,
                    'due_date' => now()->addDays(30)->format('Y-m-d'),
                    'status' => 'Pending',
                    'matching_status' => 'Unmatched',
                ]);
                $this->createExpenseJournalEntry($bill);
                $grn->update(['supplier_bill_id' => $bill->id]);
                \audit_log($bill, 'created', "Auto-created from GRN #{$grn->grn_no} completion");
            }
        }

        return $grn;
    }

    public function approveBill(int $id): SupplierBill
    {
        $bill = SupplierBill::findOrFail($id);
        if ($bill->matching_status !== 'Matched') {
            throw new \RuntimeException('Only matched invoices can be approved. Complete 3-way matching first.');
        }
        $bill->update([
            'status' => 'Approved',
            'approved_at' => now(),
            'approved_by' => auth()->user()->name ?? 'Manager',
        ]);
        \audit_log($bill, 'approved', "Supplier bill #{$bill->bill_no} approved");
        return $bill;
    }

    public function payBill(SupplierBill $bill): SupplierBill
    {
        if ($bill->matching_status !== 'Matched') {
            throw new \RuntimeException('Only matched invoices can be paid. Complete 3-way matching first.');
        }
        $bill->update(['status' => 'Paid', 'paid_at' => now()]);
        $this->createPaymentJournalEntry($bill);
        \audit_log($bill, 'paid', "Supplier bill #{$bill->bill_no} marked as paid");
        return $bill;
    }

    public function recordPayment(array $data): Payment
    {
        $bill = SupplierBill::findOrFail($data['supplier_bill_id']);
        if ($bill->matching_status !== 'Matched') {
            throw new \RuntimeException('Only matched invoices can be paid. Complete 3-way matching first.');
        }
        $newTotal = $bill->total_paid + $data['amount'];

        if ($newTotal > $bill->amount) {
            throw new \InvalidArgumentException(
                'Payment exceeds the bill amount of ₱' . number_format($bill->amount, 2)
            );
        }

        $payment = Payment::create([
            'supplier_bill_id' => $bill->id,
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'] ?? $bill->payment_method,
            'payment_date' => $data['payment_date'],
            'reference' => $data['reference'] ?? null,
        ]);

        $bill->total_paid = $newTotal;

        if ($newTotal >= $bill->amount) {
            $bill->status = 'Paid';
            $bill->paid_at = now();
            $this->createPaymentJournalEntry($bill);
        }

        $bill->save();

        \audit_log($bill, 'payment', "Payment of ₱{$data['amount']} recorded for bill #{$bill->bill_no}");

        return $payment;
    }

    public function batchPayBills(array $ids): int
    {
        $bills = SupplierBill::whereIn('id', $ids)->where('status', 'Approved')->get();
        foreach ($bills as $bill) {
            $this->payBill($bill);
        }
        return $bills->count();
    }

    public function getDashboardMetrics(): array
    {
        $paidThisMonthAmount = SupplierBill::where('status', 'Paid')
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');
        $paidThisMonthCount = SupplierBill::where('status', 'Paid')
            ->whereMonth('paid_at', now()->month)
            ->count();

        $paymentsTodayAmount = SupplierBill::where('status', 'Paid')
            ->whereDate('paid_at', today())
            ->sum('amount');
        $paymentsTodayCount = SupplierBill::where('status', 'Paid')
            ->whereDate('paid_at', today())
            ->count();

        $pendingBillsAmount = SupplierBill::where('status', 'Pending')->sum('amount');
        $pendingBillsCount = SupplierBill::where('status', 'Pending')->count();

        $totalBillsAmount = SupplierBill::where('status', '!=', 'Paid')->sum('amount');
        $totalBillsCount = SupplierBill::where('status', '!=', 'Paid')->count();

        $overdueBills = SupplierBill::whereIn('status', ['Pending', 'Approved'])
            ->whereDate('due_date', '<', now())
            ->orderBy('due_date')
            ->get();
        $overdueAmount = $overdueBills->sum('amount');
        $overdueCount = $overdueBills->count();

        $upcomingBills = SupplierBill::where('status', 'Pending')
            ->orderBy('due_date')
            ->get();

        Payment::whereMonth('payment_date', now()->month)->sum('amount');
        Payment::whereMonth('payment_date', now()->month)->count();
        Payment::whereDate('payment_date', today())->sum('amount');
        Payment::whereDate('payment_date', today())->count();

        return compact(
            'paidThisMonthAmount',
            'paidThisMonthCount',
            'paymentsTodayAmount',
            'paymentsTodayCount',
            'pendingBillsAmount',
            'pendingBillsCount',
            'totalBillsAmount',
            'totalBillsCount',
            'overdueAmount',
            'overdueCount',
            'overdueBills',
            'upcomingBills',
        );
    }

    private function createExpenseJournalEntry(SupplierBill $bill): void
    {
        $expenseAccount = ChartOfAccount::where('account_code', '5000')->first();
        $apAccount = ChartOfAccount::where('account_code', '2100')->first();

        if (!$expenseAccount || !$apAccount) {
            return;
        }

        $entry = JournalEntry::create([
            'transaction_date' => $bill->created_at ? $bill->created_at->format('Y-m-d') : now()->format('Y-m-d'),
            'reference_no' => generate_expense_ref(),
            'description' => "Expense recognition - Bill #{$bill->bill_no} - {$bill->supplier}",
            'status' => 'Posted',
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry->journal_entry_id,
            'account_id' => $expenseAccount->account_id,
            'description' => "Inventory / Purchases - {$bill->supplier} - Bill #{$bill->bill_no}",
            'debit' => $bill->amount,
            'credit' => 0,
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry->journal_entry_id,
            'account_id' => $apAccount->account_id,
            'description' => "Accounts Payable - {$bill->supplier} - Bill #{$bill->bill_no}",
            'debit' => 0,
            'credit' => $bill->amount,
        ]);
    }

    private function createPaymentJournalEntry(SupplierBill $bill): void
    {
        $apAccount = ChartOfAccount::where('account_code', '2100')->first()
            ?? ChartOfAccount::create([
                'account_code' => '2100',
                'account_name' => 'Accounts Payable',
                'type' => 'Liability',
                'normal_balance' => 'Credit',
                'status' => 'Active',
            ]);
        $expenseAccount = ChartOfAccount::where('account_code', '5000')->first();
        $cashAccount = ChartOfAccount::where('account_code', '1010')->first()
            ?? ChartOfAccount::create([
                'account_code' => '1010',
                'account_name' => 'Cash on Hand',
                'type' => 'Asset',
                'normal_balance' => 'Debit',
                'status' => 'Active',
            ]);

        $entry = JournalEntry::create([
            'transaction_date' => now(),
            'reference_no' => $bill->bill_no,
            'description' => "Payment for supplier bill #{$bill->bill_no} - {$bill->supplier}",
            'status' => 'Posted',
        ]);

        if ($expenseAccount) {
            JournalEntryLine::create([
                'journal_entry_id' => $entry->journal_entry_id,
                'account_id' => $expenseAccount->account_id,
                'description' => "Purchases - {$bill->supplier} - Bill #{$bill->bill_no}",
                'debit' => $bill->amount,
                'credit' => 0,
            ]);
        }

        JournalEntryLine::create([
            'journal_entry_id' => $entry->journal_entry_id,
            'account_id' => $apAccount->account_id,
            'description' => "Accounts Payable - {$bill->supplier} - Bill #{$bill->bill_no}",
            'debit' => 0,
            'credit' => $bill->amount,
        ]);


        JournalEntryLine::create([
            'journal_entry_id' => $entry->journal_entry_id,
            'account_id' => $apAccount->account_id,
            'description' => "Payment - {$bill->supplier} - Bill #{$bill->bill_no}",
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
