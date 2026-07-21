<?php
namespace Database\Seeders;

use App\Models\AccountPayable\GoodsReceivedNote;
use App\Models\AccountPayable\Payment;
use App\Models\AccountPayable\PurchaseOrder;
use App\Models\AccountPayable\SupplierBill;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SupplyChainSeeder extends Seeder
{
    public function run(): void
    {
        // === PURCHASE ORDER: Draft ===
        $poDraft = PurchaseOrder::create([
            'po_no' => 'PO-2026-003',
            'supplier' => 'TechWorld Solutions',
            'item_name' => 'Networking Equipment',
            'qty' => 3,
            'unit_cost' => 12000,
            'amount' => 36000,
            'description' => '[SupplyChainSeeder] Network switches and routers for office expansion',
            'order_date' => now()->format('Y-m-d'),
            'expected_delivery' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'Draft',
        ]);

        // === PURCHASE ORDER: Confirmed (awaiting delivery) ===
        $poConfirmed = PurchaseOrder::create([
            'po_no' => 'PO-2026-004',
            'supplier' => 'Global Supply Co.',
            'item_name' => 'Industrial Fans',
            'qty' => 8,
            'unit_cost' => 4500,
            'amount' => 36000,
            'description' => '[SupplyChainSeeder] Ventilation fans for warehouse',
            'order_date' => now()->subDays(3)->format('Y-m-d'),
            'expected_delivery' => now()->addDays(5)->format('Y-m-d'),
            'status' => 'Confirmed',
            'sent_at' => now()->subDays(3),
            'confirmed_at' => now()->subDays(2),
        ]);

        // === PURCHASE ORDER: Delivered with GRN ===
        $poDelivered = PurchaseOrder::create([
            'po_no' => 'PO-2026-005',
            'supplier' => 'Prime Office Supplies',
            'item_name' => 'Ergonomic Chairs',
            'qty' => 10,
            'unit_cost' => 3200,
            'amount' => 32000,
            'description' => '[SupplyChainSeeder] Ergonomic chairs for staff',
            'order_date' => now()->subDays(10)->format('Y-m-d'),
            'expected_delivery' => now()->subDays(2)->format('Y-m-d'),
            'status' => 'Delivered',
            'sent_at' => now()->subDays(10),
            'confirmed_at' => now()->subDays(9),
            'delivered_at' => now()->subDays(2),
        ]);

        // === GOODS RECEIVED NOTE for Delivered PO ===
        $grn = GoodsReceivedNote::create([
            'grn_no' => 'GRN-2026-002',
            'purchase_order_id' => $poDelivered->id,
            'po_no_ref' => $poDelivered->po_no,
            'item_name' => 'Ergonomic Chairs',
            'qty_ordered' => 10,
            'qty_received' => 10,
            'supplier' => 'Prime Office Supplies',
            'amount' => 32000,
            'received_date' => now()->subDays(2)->format('Y-m-d'),
            'status' => 'Completed',
            'notes' => '[SupplyChainSeeder]',
        ]);

        // === SUPPLIER BILL from GRN delivery ===
        $billApproved = SupplierBill::create([
            'bill_no' => 'BILL-2026-004',
            'po_no' => $poDelivered->po_no,
            'grn_no' => $grn->grn_no,
            'supplier' => 'Prime Office Supplies',
            'amount' => 32000,
            'total_paid' => 0,
            'due_date' => now()->addDays(28)->format('Y-m-d'),
            'status' => 'Approved',
            'matching_status' => 'Matched',
            'payment_method' => 'Bank Transfer',
            'approved_at' => now(),
            'approved_by' => 'Procurement Manager',
            'matching_notes' => '[SupplyChainSeeder]',
        ]);

        // === SUPPLIER BILL: Pending ===
        SupplierBill::create([
            'bill_no' => 'BILL-2026-005',
            'po_no' => $poConfirmed->po_no,
            'grn_no' => '',
            'supplier' => 'Global Supply Co.',
            'amount' => 36000,
            'total_paid' => 0,
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'Pending',
            'matching_status' => 'Unmatched',
            'payment_method' => 'Cash',
            'matching_notes' => '[SupplyChainSeeder]',
        ]);

        // === SUPPLIER BILL: Paid ===
        $billPaid = SupplierBill::create([
            'bill_no' => 'BILL-2026-006',
            'po_no' => 'PO-2026-006',
            'grn_no' => 'GRN-2026-003',
            'supplier' => 'QuickFix Services',
            'amount' => 15000,
            'total_paid' => 15000,
            'due_date' => now()->subDays(5)->format('Y-m-d'),
            'status' => 'Paid',
            'matching_status' => 'Matched',
            'payment_method' => 'Bank Transfer',
            'paid_at' => now()->subDays(3),
            'matching_notes' => '[SupplyChainSeeder]',
        ]);

        // === PAYMENT for paid bill ===
        Payment::create([
            'supplier_bill_id' => $billPaid->id,
            'amount' => 15000,
            'payment_method' => 'Bank Transfer',
            'payment_date' => now()->subDays(3)->format('Y-m-d'),
            'reference' => 'PAY-2026-002',
        ]);

        // === GOODS RECEIVED NOTE: Pending ===
        GoodsReceivedNote::create([
            'grn_no' => 'GRN-2026-004',
            'purchase_order_id' => $poConfirmed->id,
            'po_no_ref' => $poConfirmed->po_no,
            'item_name' => 'Industrial Fans',
            'qty_ordered' => 8,
            'qty_received' => 0,
            'supplier' => 'Global Supply Co.',
            'amount' => 36000,
            'received_date' => now()->addDays(5)->format('Y-m-d'),
            'status' => 'Pending',
            'notes' => '[SupplyChainSeeder]',
        ]);
    }
}
