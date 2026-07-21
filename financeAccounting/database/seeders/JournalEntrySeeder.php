<?php

namespace Database\Seeders;

use App\Models\AccountPayable\GoodsReceivedNote;
use App\Models\AccountPayable\Payment;
use App\Models\AccountPayable\PurchaseOrder;
use App\Models\AccountPayable\SupplierBill;
use App\Models\FinancialReporting\BudgetVsActual;
use App\Models\FinancialReporting\TaxRecord;
use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\GeneralLedger\JournalEntryLine;
use App\Models\Sales\SalesTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class JournalEntrySeeder extends Seeder
{
    public function run(): void
    {
        $coa = ChartOfAccount::pluck('account_id', 'account_code');

        $cash    = $coa['1010'] ?? null;
        $ar      = $coa['1100'] ?? null;
        $inv     = $coa['1200'] ?? null;
        $ap      = $coa['2100'] ?? null;
        $vat     = $coa['2300'] ?? null;
        $revenue = $coa['4100'] ?? null;
        $cogs    = $coa['5000'] ?? null;
        $salaries = $coa['6100'] ?? null;
        $rent    = $coa['6200'] ?? null;

        if (!$cash || !$ap || !$revenue || !$cogs) return;

        $now = Carbon::parse('2026-07-21');
        $julyStart = '2026-07-01';
        $julyEnd = '2026-07-31';

        // === JOURNAL ENTRIES === //

        $je1 = JournalEntry::create([
            'transaction_date' => '2026-07-05',
            'reference_no' => 'JE-2026-001',
            'description' => 'Sales revenue - various customers',
            'status' => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je1->journal_entry_id, 'account_id' => $cash, 'description' => 'Cash sales', 'debit' => 200000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je1->journal_entry_id, 'account_id' => $revenue, 'description' => 'Sales revenue', 'debit' => 0, 'credit' => 200000]);

        $je2 = JournalEntry::create([
            'transaction_date' => '2026-07-06',
            'reference_no' => 'JE-2026-002',
            'description' => 'Purchase of merchandise inventory',
            'status' => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je2->journal_entry_id, 'account_id' => $cogs, 'description' => 'Inventory purchase', 'debit' => 80000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je2->journal_entry_id, 'account_id' => $ap, 'description' => 'Accounts Payable - EASY PC', 'debit' => 0, 'credit' => 80000]);

        $je3 = JournalEntry::create([
            'transaction_date' => '2026-07-10',
            'reference_no' => 'JE-2026-003',
            'description' => 'Salaries for first half of July',
            'status' => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je3->journal_entry_id, 'account_id' => $salaries, 'description' => 'Salaries expense', 'debit' => 30000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je3->journal_entry_id, 'account_id' => $cash, 'description' => 'Salary disbursement', 'debit' => 0, 'credit' => 30000]);

        $je4 = JournalEntry::create([
            'transaction_date' => '2026-07-01',
            'reference_no' => 'JE-2026-004',
            'description' => 'Monthly rent for office space',
            'status' => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je4->journal_entry_id, 'account_id' => $rent, 'description' => 'Rent expense', 'debit' => 15000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je4->journal_entry_id, 'account_id' => $cash, 'description' => 'Rent payment', 'debit' => 0, 'credit' => 15000]);

        $je5 = JournalEntry::create([
            'transaction_date' => '2026-07-15',
            'reference_no' => 'JE-2026-005',
            'description' => 'Partial payment to supplier',
            'status' => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je5->journal_entry_id, 'account_id' => $ap, 'description' => 'Payment to EASY PC', 'debit' => 50000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je5->journal_entry_id, 'account_id' => $cash, 'description' => 'Cash payment', 'debit' => 0, 'credit' => 50000]);

        // === PURCHASE ORDERS === //

        $po1 = PurchaseOrder::create([
            'po_no' => 'PO-2026-001',
            'supplier' => 'EASY PC',
            'item_name' => 'Laptop Units',
            'qty' => 5,
            'unit_cost' => 16000,
            'amount' => 80000,
            'description' => 'Office laptop units for new hires',
            'order_date' => '2026-07-01',
            'expected_delivery' => '2026-07-10',
            'status' => 'Delivered',
            'sent_at' => '2026-07-01 10:00:00',
            'confirmed_at' => '2026-07-02 14:00:00',
            'delivered_at' => '2026-07-10 09:00:00',
        ]);

        $po2 = PurchaseOrder::create([
            'po_no' => 'PO-2026-002',
            'supplier' => 'Office Mart',
            'item_name' => 'Office Supplies Bundle',
            'qty' => 10,
            'unit_cost' => 2500,
            'amount' => 25000,
            'description' => 'Office supplies for the month',
            'order_date' => '2026-07-05',
            'expected_delivery' => '2026-07-15',
            'status' => 'Confirmed',
            'sent_at' => '2026-07-05 11:00:00',
            'confirmed_at' => '2026-07-06 08:00:00',
        ]);

        // === GOODS RECEIVED NOTES === //

        GoodsReceivedNote::create([
            'grn_no' => 'GRN-2026-001',
            'purchase_order_id' => $po1->id,
            'po_no_ref' => $po1->po_no,
            'item_name' => 'Laptop Units',
            'qty_ordered' => 5,
            'qty_received' => 5,
            'supplier' => 'EASY PC',
            'amount' => 80000,
            'received_date' => '2026-07-10',
            'status' => 'Completed',
        ]);

        // === SUPPLIER BILLS === //

        $bill1 = SupplierBill::create([
            'bill_no' => 'BILL-2026-001',
            'po_no' => $po1->po_no,
            'grn_no' => 'GRN-2026-001',
            'supplier' => 'EASY PC',
            'amount' => 80000,
            'total_paid' => 50000,
            'due_date' => '2026-08-10',
            'status' => 'Paid',
            'matching_status' => 'Matched',
            'payment_method' => 'Bank Transfer',
            'paid_at' => '2026-07-15 10:00:00',
        ]);

        SupplierBill::create([
            'bill_no' => 'BILL-2026-002',
            'po_no' => $po2->po_no,
            'grn_no' => '',
            'supplier' => 'Office Mart',
            'amount' => 25000,
            'total_paid' => 0,
            'due_date' => '2026-08-15',
            'status' => 'Pending',
            'matching_status' => 'Unmatched',
            'payment_method' => 'Cash',
        ]);

        // === PAYMENT === //

        Payment::create([
            'supplier_bill_id' => $bill1->id,
            'amount' => 50000,
            'payment_method' => 'Bank Transfer',
            'payment_date' => '2026-07-15',
            'reference' => 'PAY-2026-001',
        ]);

        // === SALES TRANSACTIONS === //

        SalesTransaction::create([
            'order_no' => 'SO-2026-001',
            'customer_name' => 'Juan Dela Cruz',
            'total_amount' => 50000,
            'payment_method' => 'Cash',
            'status' => 'Paid',
            'is_posted_to_finance' => true,
        ]);

        SalesTransaction::create([
            'order_no' => 'SO-2026-002',
            'customer_name' => 'Maria Santos',
            'total_amount' => 35000,
            'payment_method' => 'Installment',
            'status' => 'Pending',
            'is_posted_to_finance' => false,
        ]);

        SalesTransaction::create([
            'order_no' => 'SO-2026-003',
            'customer_name' => 'Pedro Reyes',
            'total_amount' => 75000,
            'payment_method' => 'Cash',
            'status' => 'Paid',
            'is_posted_to_finance' => true,
        ]);

        // === BUDGET ENTRIES === //

        BudgetVsActual::create([
            'account_id' => $revenue,
            'account_name' => 'Sales Revenue',
            'budget_amount' => 200000,
            'actual_amount' => 0,
            'report_period_start' => $julyStart,
            'report_period_end' => $julyEnd,
        ]);

        BudgetVsActual::create([
            'account_id' => $cogs,
            'account_name' => 'Purchases / COGS',
            'budget_amount' => 100000,
            'actual_amount' => 0,
            'report_period_start' => $julyStart,
            'report_period_end' => $julyEnd,
        ]);

        BudgetVsActual::create([
            'account_id' => $salaries,
            'account_name' => 'Salaries and Wages',
            'budget_amount' => 35000,
            'actual_amount' => 0,
            'report_period_start' => $julyStart,
            'report_period_end' => $julyEnd,
        ]);

        // === TAX RECORDS === //

        TaxRecord::create([
            'reference_type' => 'Journal Entry',
            'reference_id' => $je1->journal_entry_id,
            'tax_type' => 'VAT',
            'taxable_amount' => 200000,
            'tax_rate' => 12.00,
            'tax_amount' => 24000,
            'tax_period' => 'July 2026',
            'filing_status' => 'filed',
        ]);

        TaxRecord::create([
            'reference_type' => 'Supplier Bill',
            'reference_id' => $bill1->id,
            'tax_type' => 'VAT',
            'taxable_amount' => 80000,
            'tax_rate' => 12.00,
            'tax_amount' => 9600,
            'tax_period' => 'July 2026',
            'filing_status' => 'pending',
        ]);
    }
}
