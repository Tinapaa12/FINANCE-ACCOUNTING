<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DemoDataController extends Controller
{
    public function seed(Request $request)
    {
        if ($request->header('X-API-Key') !== config('app.management_api_key')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        TaxRecord::truncate();
        BudgetVsActual::truncate();
        JournalEntryLine::truncate();
        JournalEntry::truncate();
        Payment::truncate();
        SupplierBill::truncate();
        GoodsReceivedNote::truncate();
        PurchaseOrder::truncate();
        SalesTransaction::truncate();
        ChartOfAccount::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $accounts = [
            ['account_code' => '1010', 'account_name' => 'Cash on Hand',           'normal_balance' => 'Debit',  'type' => 'Asset',     'status' => 'Active'],
            ['account_code' => '1020', 'account_name' => 'Cash in Bank - BDO',      'normal_balance' => 'Debit',  'type' => 'Asset',     'status' => 'Active'],
            ['account_code' => '1100', 'account_name' => 'Accounts Receivable',     'normal_balance' => 'Debit',  'type' => 'Asset',     'status' => 'Active'],
            ['account_code' => '1200', 'account_name' => 'Merchandise Inventory',   'normal_balance' => 'Debit',  'type' => 'Asset',     'status' => 'Active'],
            ['account_code' => '2100', 'account_name' => 'Accounts Payable',        'normal_balance' => 'Credit', 'type' => 'Liability',  'status' => 'Active'],
            ['account_code' => '2300', 'account_name' => 'Output VAT Payable',      'normal_balance' => 'Credit', 'type' => 'Liability',  'status' => 'Active'],
            ['account_code' => '3100', 'account_name' => 'Retained Earnings',       'normal_balance' => 'Credit', 'type' => 'Equity',    'status' => 'Active'],
            ['account_code' => '4100', 'account_name' => 'Sales Revenue',           'normal_balance' => 'Credit', 'type' => 'Revenue',   'status' => 'Active'],
            ['account_code' => '5000', 'account_name' => 'Purchases / COGS',        'normal_balance' => 'Debit',  'type' => 'Expense',   'status' => 'Active'],
            ['account_code' => '6100', 'account_name' => 'Salaries and Wages',      'normal_balance' => 'Debit',  'type' => 'Expense',   'status' => 'Active'],
            ['account_code' => '6200', 'account_name' => 'Rent Expenses',           'normal_balance' => 'Debit',  'type' => 'Expense',   'status' => 'Active'],
        ];

        foreach ($accounts as $a) {
            ChartOfAccount::create($a);
        }

        $coa = ChartOfAccount::pluck('account_id', 'account_code');
        $cash     = $coa['1010'];
        $ar       = $coa['1100'];
        $ap       = $coa['2100'];
        $revenue  = $coa['4100'];
        $cogs     = $coa['5000'];
        $salaries = $coa['6100'];
        $rent     = $coa['6200'];

        $je1 = JournalEntry::create(['transaction_date' => '2026-07-05', 'reference_no' => 'JE-2026-001', 'description' => 'Sales revenue - walk-in customers', 'status' => 'Posted']);
        JournalEntryLine::create(['journal_entry_id' => $je1->journal_entry_id, 'account_id' => $cash,    'description' => 'Cash sales',           'debit' => 200000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je1->journal_entry_id, 'account_id' => $revenue, 'description' => 'Service revenue',       'debit' => 0,      'credit' => 200000]);

        $je2 = JournalEntry::create(['transaction_date' => '2026-07-06', 'reference_no' => 'JE-2026-002', 'description' => 'Purchase of inventory on credit', 'status' => 'Posted']);
        JournalEntryLine::create(['journal_entry_id' => $je2->journal_entry_id, 'account_id' => $cogs, 'description' => 'Inventory purchase',     'debit' => 80000,  'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je2->journal_entry_id, 'account_id' => $ap,   'description' => 'AP - EASY PC',          'debit' => 0,      'credit' => 80000]);

        $je3 = JournalEntry::create(['transaction_date' => '2026-07-10', 'reference_no' => 'JE-2026-003', 'description' => 'Salaries - first half July', 'status' => 'Posted']);
        JournalEntryLine::create(['journal_entry_id' => $je3->journal_entry_id, 'account_id' => $salaries, 'description' => 'Salary expense',        'debit' => 30000,  'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je3->journal_entry_id, 'account_id' => $cash,     'description' => 'Salary disbursement',   'debit' => 0,      'credit' => 30000]);

        $je4 = JournalEntry::create(['transaction_date' => '2026-07-01', 'reference_no' => 'JE-2026-004', 'description' => 'Monthly office rent', 'status' => 'Posted']);
        JournalEntryLine::create(['journal_entry_id' => $je4->journal_entry_id, 'account_id' => $rent, 'description' => 'Rent expense',           'debit' => 15000,  'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je4->journal_entry_id, 'account_id' => $cash, 'description' => 'Rent payment',           'debit' => 0,      'credit' => 15000]);

        $je5 = JournalEntry::create(['transaction_date' => '2026-07-15', 'reference_no' => 'JE-2026-005', 'description' => 'Partial payment to supplier', 'status' => 'Posted']);
        JournalEntryLine::create(['journal_entry_id' => $je5->journal_entry_id, 'account_id' => $ap,   'description' => 'Payment to EASY PC',     'debit' => 50000,  'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je5->journal_entry_id, 'account_id' => $cash, 'description' => 'Cash disbursement',      'debit' => 0,      'credit' => 50000]);

        $po1 = PurchaseOrder::create(['po_no' => 'PO-2026-001', 'supplier' => 'EASY PC',  'item_name' => 'Laptop Units',        'qty' => 5,  'unit_cost' => 16000, 'amount' => 80000,  'description' => 'Laptops for new hires',       'order_date' => '2026-07-01', 'expected_delivery' => '2026-07-10', 'status' => 'Delivered',  'sent_at' => '2026-07-01 10:00:00', 'confirmed_at' => '2026-07-02 14:00:00', 'delivered_at' => '2026-07-10 09:00:00']);
        $po2 = PurchaseOrder::create(['po_no' => 'PO-2026-002', 'supplier' => 'Office Mart', 'item_name' => 'Office Supplies Bundle', 'qty' => 10, 'unit_cost' => 2500,  'amount' => 25000,  'description' => 'Monthly office supplies',    'order_date' => '2026-07-05', 'expected_delivery' => '2026-07-15', 'status' => 'Confirmed', 'sent_at' => '2026-07-05 11:00:00', 'confirmed_at' => '2026-07-06 08:00:00']);

        GoodsReceivedNote::create(['grn_no' => 'GRN-2026-001', 'purchase_order_id' => $po1->id, 'po_no_ref' => $po1->po_no, 'item_name' => 'Laptop Units', 'qty_ordered' => 5, 'qty_received' => 5, 'supplier' => 'EASY PC', 'amount' => 80000, 'received_date' => '2026-07-10', 'status' => 'Completed']);

        $bill1 = SupplierBill::create(['bill_no' => 'BILL-2026-001', 'po_no' => $po1->po_no, 'grn_no' => 'GRN-2026-001', 'supplier' => 'EASY PC', 'amount' => 80000, 'total_paid' => 50000, 'due_date' => '2026-08-10', 'status' => 'Paid', 'matching_status' => 'Matched', 'payment_method' => 'Bank Transfer', 'paid_at' => '2026-07-15 10:00:00']);
        SupplierBill::create(['bill_no' => 'BILL-2026-002', 'po_no' => $po2->po_no, 'grn_no' => '', 'supplier' => 'Office Mart', 'amount' => 25000, 'total_paid' => 0, 'due_date' => '2026-08-15', 'status' => 'Pending', 'matching_status' => 'Unmatched', 'payment_method' => 'Cash']);

        Payment::create(['supplier_bill_id' => $bill1->id, 'amount' => 50000, 'payment_method' => 'Bank Transfer', 'payment_date' => '2026-07-15', 'reference' => 'PAY-2026-001']);

        SalesTransaction::create(['order_no' => 'SO-2026-001', 'customer_name' => 'Juan Dela Cruz',  'total_amount' => 50000, 'payment_method' => 'Cash',        'status' => 'Paid',    'is_posted_to_finance' => true]);
        SalesTransaction::create(['order_no' => 'SO-2026-002', 'customer_name' => 'Maria Santos',    'total_amount' => 35000, 'payment_method' => 'Credit Card', 'status' => 'Pending',  'is_posted_to_finance' => false]);
        SalesTransaction::create(['order_no' => 'SO-2026-003', 'customer_name' => 'Pedro Reyes',     'total_amount' => 75000, 'payment_method' => 'Bank Transfer','status' => 'Paid',    'is_posted_to_finance' => true]);

        BudgetVsActual::create(['account_id' => $revenue,  'account_name' => 'Sales Revenue',        'budget_amount' => 200000, 'actual_amount' => 0, 'report_period_start' => '2026-07-01', 'report_period_end' => '2026-07-31']);
        BudgetVsActual::create(['account_id' => $cogs,     'account_name' => 'Purchases / COGS',     'budget_amount' => 100000, 'actual_amount' => 0, 'report_period_start' => '2026-07-01', 'report_period_end' => '2026-07-31']);
        BudgetVsActual::create(['account_id' => $salaries, 'account_name' => 'Salaries and Wages',   'budget_amount' => 35000,  'actual_amount' => 0, 'report_period_start' => '2026-07-01', 'report_period_end' => '2026-07-31']);
        BudgetVsActual::create(['account_id' => $rent,     'account_name' => 'Rent Expenses',        'budget_amount' => 18000,  'actual_amount' => 0, 'report_period_start' => '2026-07-01', 'report_period_end' => '2026-07-31']);

        TaxRecord::create(['reference_type' => 'Journal Entry',  'reference_id' => $je1->journal_entry_id, 'tax_type' => 'VAT', 'taxable_amount' => 200000, 'tax_rate' => 12.00, 'tax_amount' => 24000, 'tax_period' => 'July 2026', 'filing_status' => 'filed']);
        TaxRecord::create(['reference_type' => 'Supplier Bill',  'reference_id' => $bill1->id,             'tax_type' => 'VAT', 'taxable_amount' => 80000,  'tax_rate' => 12.00, 'tax_amount' => 9600,  'tax_period' => 'July 2026', 'filing_status' => 'pending']);

        return response()->json([
            'message' => 'Demo data seeded successfully',
            'stats' => [
                'chart_of_accounts' => ChartOfAccount::count(),
                'journal_entries' => JournalEntry::count(),
                'journal_entry_lines' => JournalEntryLine::count(),
                'purchase_orders' => PurchaseOrder::count(),
                'goods_received_notes' => GoodsReceivedNote::count(),
                'supplier_bills' => SupplierBill::count(),
                'payments' => Payment::count(),
                'sales_transactions' => SalesTransaction::count(),
                'budget_entries' => BudgetVsActual::count(),
                'tax_records' => TaxRecord::count(),
            ],
        ]);
    }

    public function migrateFresh(Request $request)
    {
        if ($request->header('X-API-Key') !== config('app.management_api_key')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        $output = Artisan::output();

        return response()->json([
            'message' => 'migrate:fresh --seed completed',
            'output' => $output,
        ]);
    }
}