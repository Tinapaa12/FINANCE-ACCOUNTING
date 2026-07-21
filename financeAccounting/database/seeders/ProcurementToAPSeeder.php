<?php

namespace Database\Seeders;

use App\Models\AccountPayable\GoodsReceivedNote;
use App\Models\AccountPayable\Payment;
use App\Models\AccountPayable\PurchaseOrder;
use App\Models\AccountPayable\SupplierBill;
use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\GeneralLedger\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProcurementToAPSeeder extends Seeder
{
    public function run(): void
    {
        $coa = ChartOfAccount::pluck('account_id', 'account_code');

        $inventory = $coa['1200'] ?? null;
        $ap        = $coa['2100'] ?? null;
        $vatOutput = $coa['2300'] ?? null;
        $cogs      = $coa['5000'] ?? null;
        $cash      = $coa['1010'] ?? null;

        if (!$inventory || !$ap || !$vatOutput || !$cogs || !$cash) {
            return;
        }

        $now = Carbon::parse('2026-07-21');

        // === Transaction 1: Computer Parts from PC Express (fully paid) ===

        $po1 = PurchaseOrder::create([
            'po_no'             => 'PO-2026-012',
            'supplier'          => 'PC Express Inc.',
            'item_name'         => 'Desktop Computer Units',
            'qty'               => 10,
            'unit_cost'         => 45000,
            'amount'            => 450000,
            'description'       => 'Desktop computers for accounting department',
            'order_date'        => '2026-06-15',
            'expected_delivery' => '2026-06-25',
            'status'            => 'Delivered',
            'sent_at'           => '2026-06-15 09:00:00',
            'confirmed_at'      => '2026-06-16 14:00:00',
            'delivered_at'      => '2026-06-24 11:00:00',
        ]);

        GoodsReceivedNote::create([
            'grn_no'            => 'GRN-2026-007',
            'purchase_order_id' => $po1->id,
            'po_no_ref'         => $po1->po_no,
            'item_name'         => 'Desktop Computer Units',
            'qty_ordered'       => 10,
            'qty_received'      => 10,
            'supplier'          => 'PC Express Inc.',
            'amount'            => 450000,
            'received_date'     => '2026-06-24',
            'status'            => 'Completed',
        ]);

        $bill1 = SupplierBill::create([
            'bill_no'         => 'BILL-2026-008',
            'po_no'           => $po1->po_no,
            'grn_no'          => 'GRN-2026-007',
            'supplier'        => 'PC Express Inc.',
            'amount'          => 450000,
            'total_paid'      => 450000,
            'due_date'        => '2026-07-25',
            'status'          => 'Paid',
            'matching_status' => 'Matched',
            'payment_method'  => 'Bank Transfer',
            'paid_at'         => '2026-07-05 14:00:00',
            'approved_at'     => '2026-06-28 10:00:00',
            'approved_by'     => 'Finance Manager',
            'ewt_rate'        => 1.00,
            'payment_terms'   => '30 days',
        ]);

        Payment::create([
            'supplier_bill_id' => $bill1->id,
            'amount'           => 450000,
            'payment_method'   => 'Bank Transfer',
            'payment_date'     => '2026-07-05',
            'reference'        => 'PAY-2026-005',
            'notes'            => 'Full payment PC Express - BDO transfer',
        ]);

        // JE: Dr Inventory 450,000 / Cr AP 450,000 (expense JE on bill approval)
        $je1 = JournalEntry::create([
            'transaction_date' => '2026-06-28',
            'reference_no'     => 'JE-2026-006',
            'description'      => 'Supplier invoice - PC Express (10 desktop units)',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je1->journal_entry_id, 'account_id' => $inventory, 'description' => 'Desktop computers received', 'debit' => 450000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je1->journal_entry_id, 'account_id' => $ap, 'description' => 'PC Express - accounts payable', 'debit' => 0, 'credit' => 450000]);

        $je1a = JournalEntry::create([
            'transaction_date' => '2026-07-05',
            'reference_no'     => 'JE-2026-007',
            'description'      => 'Payment to PC Express (full settlement)',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je1a->journal_entry_id, 'account_id' => $ap, 'description' => 'Payment to PC Express', 'debit' => 450000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je1a->journal_entry_id, 'account_id' => $cash, 'description' => 'Cash disbursement - BDO', 'debit' => 0, 'credit' => 450000]);

        // === Transaction 2: Office Furniture from Crown Furniture (pending) ===

        $po2 = PurchaseOrder::create([
            'po_no'             => 'PO-2026-013',
            'supplier'          => 'Crown Furniture Corp.',
            'item_name'         => 'Executive Office Chairs',
            'qty'               => 15,
            'unit_cost'         => 8500,
            'amount'            => 127500,
            'description'       => 'Ergonomic chairs for office renovation',
            'order_date'        => '2026-07-01',
            'expected_delivery' => '2026-07-12',
            'status'            => 'Delivered',
            'sent_at'           => '2026-07-01 10:30:00',
            'confirmed_at'      => '2026-07-02 09:00:00',
            'delivered_at'      => '2026-07-11 15:00:00',
        ]);

        GoodsReceivedNote::create([
            'grn_no'            => 'GRN-2026-008',
            'purchase_order_id' => $po2->id,
            'po_no_ref'         => $po2->po_no,
            'item_name'         => 'Executive Office Chairs',
            'qty_ordered'       => 15,
            'qty_received'      => 15,
            'supplier'          => 'Crown Furniture Corp.',
            'amount'            => 127500,
            'received_date'     => '2026-07-11',
            'status'            => 'Completed',
        ]);

        $bill2 = SupplierBill::create([
            'bill_no'         => 'BILL-2026-009',
            'po_no'           => $po2->po_no,
            'grn_no'          => 'GRN-2026-008',
            'supplier'        => 'Crown Furniture Corp.',
            'amount'          => 127500,
            'total_paid'      => 0,
            'due_date'        => '2026-08-11',
            'status'          => 'Approved',
            'matching_status' => 'Matched',
            'payment_method'  => 'Bank Transfer',
            'approved_at'     => '2026-07-14 14:00:00',
            'approved_by'     => 'Finance Manager',
            'payment_terms'   => '30 days',
        ]);

        $je2 = JournalEntry::create([
            'transaction_date' => '2026-07-14',
            'reference_no'     => 'JE-2026-008',
            'description'      => 'Supplier invoice - Crown Furniture (15 office chairs)',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je2->journal_entry_id, 'account_id' => $inventory, 'description' => 'Office chairs received', 'debit' => 127500, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je2->journal_entry_id, 'account_id' => $ap, 'description' => 'Crown Furniture - accounts payable', 'debit' => 0, 'credit' => 127500]);

        // === Transaction 3: Raw Materials from Manila Paper Supply (partially paid) ===

        $po3 = PurchaseOrder::create([
            'po_no'             => 'PO-2026-014',
            'supplier'          => 'Manila Paper Supply Co.',
            'item_name'         => 'Premium Bond Paper (reams)',
            'qty'               => 200,
            'unit_cost'         => 180,
            'amount'            => 36000,
            'description'       => 'Bond paper for Q3 office supplies',
            'order_date'        => '2026-07-05',
            'expected_delivery' => '2026-07-10',
            'status'            => 'Delivered',
            'sent_at'           => '2026-07-05 08:00:00',
            'confirmed_at'      => '2026-07-05 16:00:00',
            'delivered_at'      => '2026-07-09 10:00:00',
        ]);

        GoodsReceivedNote::create([
            'grn_no'            => 'GRN-2026-009',
            'purchase_order_id' => $po3->id,
            'po_no_ref'         => $po3->po_no,
            'item_name'         => 'Premium Bond Paper (reams)',
            'qty_ordered'       => 200,
            'qty_received'      => 200,
            'supplier'          => 'Manila Paper Supply Co.',
            'amount'            => 36000,
            'received_date'     => '2026-07-09',
            'status'            => 'Completed',
        ]);

        $bill3 = SupplierBill::create([
            'bill_no'         => 'BILL-2026-010',
            'po_no'           => $po3->po_no,
            'grn_no'          => 'GRN-2026-009',
            'supplier'        => 'Manila Paper Supply Co.',
            'amount'          => 36000,
            'total_paid'      => 20000,
            'due_date'        => '2026-08-09',
            'status'          => 'Paid',
            'matching_status' => 'Matched',
            'payment_method'  => 'Cash',
            'paid_at'         => '2026-07-18 09:00:00',
            'approved_at'     => '2026-07-12 11:00:00',
            'approved_by'     => 'Finance Manager',
            'payment_terms'   => '30 days',
        ]);

        Payment::create([
            'supplier_bill_id' => $bill3->id,
            'amount'           => 20000,
            'payment_method'   => 'Cash',
            'payment_date'     => '2026-07-18',
            'reference'        => 'PAY-2026-006',
            'notes'            => 'Partial payment - remaining balance ₱16,000',
        ]);

        $je3 = JournalEntry::create([
            'transaction_date' => '2026-07-12',
            'reference_no'     => 'JE-2026-009',
            'description'      => 'Supplier invoice - Manila Paper Supply (bond paper)',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je3->journal_entry_id, 'account_id' => $cogs, 'description' => 'Office supplies - bond paper', 'debit' => 36000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je3->journal_entry_id, 'account_id' => $ap, 'description' => 'Manila Paper Supply - accounts payable', 'debit' => 0, 'credit' => 36000]);

        $je3a = JournalEntry::create([
            'transaction_date' => '2026-07-18',
            'reference_no'     => 'JE-2026-010',
            'description'      => 'Partial payment to Manila Paper Supply',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je3a->journal_entry_id, 'account_id' => $ap, 'description' => 'Partial payment to Manila Paper Supply', 'debit' => 20000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je3a->journal_entry_id, 'account_id' => $cash, 'description' => 'Cash payment', 'debit' => 0, 'credit' => 20000]);

        // === Transaction 4: Aircon Units from Aircon Experts PH (pending/approved) ===

        $po4 = PurchaseOrder::create([
            'po_no'             => 'PO-2026-015',
            'supplier'          => 'Aircon Experts PH Inc.',
            'item_name'         => 'Inverter Air Conditioner 2.5HP',
            'qty'               => 4,
            'unit_cost'         => 38500,
            'amount'            => 154000,
            'description'       => 'AC units for server room and office areas',
            'order_date'        => '2026-07-10',
            'expected_delivery' => '2026-07-20',
            'status'            => 'Delivered',
            'sent_at'           => '2026-07-10 14:00:00',
            'confirmed_at'      => '2026-07-11 10:00:00',
            'delivered_at'      => '2026-07-19 16:00:00',
        ]);

        GoodsReceivedNote::create([
            'grn_no'            => 'GRN-2026-010',
            'purchase_order_id' => $po4->id,
            'po_no_ref'         => $po4->po_no,
            'item_name'         => 'Inverter Air Conditioner 2.5HP',
            'qty_ordered'       => 4,
            'qty_received'      => 4,
            'supplier'          => 'Aircon Experts PH Inc.',
            'amount'            => 154000,
            'received_date'     => '2026-07-19',
            'status'            => 'Completed',
        ]);

        $bill4 = SupplierBill::create([
            'bill_no'         => 'BILL-2026-011',
            'po_no'           => $po4->po_no,
            'grn_no'          => 'GRN-2026-010',
            'supplier'        => 'Aircon Experts PH Inc.',
            'amount'          => 154000,
            'total_paid'      => 0,
            'due_date'        => '2026-08-19',
            'status'          => 'Pending',
            'matching_status' => 'Unmatched',
            'payment_method'  => 'Bank Transfer',
            'payment_terms'   => '30 days',
        ]);

        // === Transaction 5: Cleaning Supplies from CleanPro (fully paid, direct expense) ===

        $po5 = PurchaseOrder::create([
            'po_no'             => 'PO-2026-016',
            'supplier'          => 'CleanPro Solutions Inc.',
            'item_name'         => 'Janitorial Supplies Bundle',
            'qty'               => 25,
            'unit_cost'         => 750,
            'amount'            => 18750,
            'description'       => 'Monthly janitorial supplies for office',
            'order_date'        => '2026-07-14',
            'expected_delivery' => '2026-07-16',
            'status'            => 'Delivered',
            'sent_at'           => '2026-07-14 09:00:00',
            'confirmed_at'      => '2026-07-14 15:00:00',
            'delivered_at'      => '2026-07-16 11:00:00',
        ]);

        GoodsReceivedNote::create([
            'grn_no'            => 'GRN-2026-011',
            'purchase_order_id' => $po5->id,
            'po_no_ref'         => $po5->po_no,
            'item_name'         => 'Janitorial Supplies Bundle',
            'qty_ordered'       => 25,
            'qty_received'      => 25,
            'supplier'          => 'CleanPro Solutions Inc.',
            'amount'            => 18750,
            'received_date'     => '2026-07-16',
            'status'            => 'Completed',
        ]);

        $bill5 = SupplierBill::create([
            'bill_no'         => 'BILL-2026-012',
            'po_no'           => $po5->po_no,
            'grn_no'          => 'GRN-2026-011',
            'supplier'        => 'CleanPro Solutions Inc.',
            'amount'          => 18750,
            'total_paid'      => 18750,
            'due_date'        => '2026-08-16',
            'status'          => 'Paid',
            'matching_status' => 'Matched',
            'payment_method'  => 'Cash',
            'paid_at'         => '2026-07-17 10:00:00',
            'approved_at'     => '2026-07-16 16:00:00',
            'approved_by'     => 'Finance Manager',
            'payment_terms'   => 'Immediate',
        ]);

        Payment::create([
            'supplier_bill_id' => $bill5->id,
            'amount'           => 18750,
            'payment_method'   => 'Cash',
            'payment_date'     => '2026-07-17',
            'reference'        => 'PAY-2026-007',
            'notes'            => 'Full payment - CleanPro supplies',
        ]);

        $je5 = JournalEntry::create([
            'transaction_date' => '2026-07-16',
            'reference_no'     => 'JE-2026-011',
            'description'      => 'Supplier invoice - CleanPro Solutions (janitorial supplies)',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je5->journal_entry_id, 'account_id' => $cogs, 'description' => 'Janitorial supplies expense', 'debit' => 18750, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je5->journal_entry_id, 'account_id' => $ap, 'description' => 'CleanPro Solutions - accounts payable', 'debit' => 0, 'credit' => 18750]);

        $je5a = JournalEntry::create([
            'transaction_date' => '2026-07-17',
            'reference_no'     => 'JE-2026-012',
            'description'      => 'Payment to CleanPro Solutions (full settlement)',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je5a->journal_entry_id, 'account_id' => $ap, 'description' => 'Payment to CleanPro Solutions', 'debit' => 18750, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je5a->journal_entry_id, 'account_id' => $cash, 'description' => 'Cash disbursement', 'debit' => 0, 'credit' => 18750]);
    }
}
