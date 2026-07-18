<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\AccountPayable\GoodsReceivedNote;
use App\Models\AccountPayable\PurchaseOrder;
use App\Models\AccountPayable\SupplierBill;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::whereIn('status', ['Confirmed', 'Delivered'])->orderBy('created_at', 'desc')->get();
        $grns = GoodsReceivedNote::with('purchaseOrder')->orderBy('created_at', 'desc')->get();
        $bills = SupplierBill::orderBy('created_at', 'desc')->get();
        $matched = SupplierBill::where('matching_status', 'Matched')
            ->whereIn('status', ['Pending', 'Approved'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Procurement.matching.index', compact('purchaseOrders', 'grns', 'bills', 'matched'));
    }

    public function match(Request $request)
    {
        $data = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'goods_received_note_id' => 'required|exists:goods_received_notes,id',
            'supplier_bill_id' => 'required|exists:supplier_bills,id',
        ]);

        $po = PurchaseOrder::findOrFail($data['purchase_order_id']);
        $grn = GoodsReceivedNote::findOrFail($data['goods_received_note_id']);
        $bill = SupplierBill::findOrFail($data['supplier_bill_id']);

        $issues = [];

        // 1. PO amount vs Bill amount
        if (abs((float) $po->amount - (float) $bill->amount) > 0.01) {
            $issues[] = "PO amount (₱{$po->amount}) differs from Bill amount (₱{$bill->amount})";
        }

        // 2. GRN amount (qty_received * unit_cost) vs Bill amount
        $grnValue = $grn->qty_received * ($po->unit_cost ?? 0);
        if (abs($grnValue - (float) $bill->amount) > 0.01) {
            $issues[] = "GRN value (₱{$grnValue}) differs from Bill amount (₱{$bill->amount})";
        }

        // 3. GRN qty vs PO qty
        if ($grn->qty_received != $po->qty) {
            $issues[] = "GRN qty ({$grn->qty_received}) differs from PO qty ({$po->qty})";
        }

        // Determine matching status
        if (empty($issues)) {
            $status = 'Matched';
            $notes = 'All criteria match (PO amount, GRN value, GRN qty).';
        } elseif (count($issues) <= 2) {
            $status = 'Partially Matched';
            $notes = implode('; ', $issues);
        } else {
            $status = 'Flagged';
            $notes = implode('; ', $issues);
        }

        $bill->update([
            'matching_status' => $status,
            'matching_notes' => $notes,
            'po_no' => $bill->po_no ?: $po->po_no,
        ]);

        if ($grn->supplier_bill_id !== $bill->id) {
            $grn->update(['supplier_bill_id' => $bill->id]);
        }

        \audit_log($bill, 'matched', "3-way match: {$status} — {$notes}");

        return redirect()->route('procurement.matching.index')
            ->with('success', "3-Way Match completed: {$status}.");
    }

    public function resolve($id)
    {
        $bill = SupplierBill::findOrFail($id);
        $bill->update([
            'matching_status' => 'Matched',
            'matching_notes' => 'Manually resolved as Matched.',
        ]);

        \audit_log($bill, 'matched', "3-way match manually resolved to Matched");

        return redirect()->route('procurement.matching.index')
            ->with('success', "Bill #{$bill->bill_no} manually resolved as Matched.");
    }
}
