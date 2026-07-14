<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceivedNote;
use App\Models\SupplierBill;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::orderBy('created_at', 'desc')->paginate(6);
        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier' => 'required',
            'amount' => 'required|numeric',
            'order_date' => 'required|date',
            'expected_delivery' => 'nullable|date',
            'description' => 'nullable|string',
            'status' => 'required',
        ]);

        $nextId = PurchaseOrder::count() + 1;

        PurchaseOrder::create([
            'po_no' => 'PO-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT),
            'supplier' => $request->supplier,
            'amount' => $request->amount,
            'order_date' => $request->order_date,
            'expected_delivery' => $request->expected_delivery,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('supplier-bills.index', ['tab' => 'pos']);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'supplier' => 'required',
            'amount' => 'required|numeric',
            'order_date' => 'required|date',
            'expected_delivery' => 'nullable|date',
            'description' => 'nullable|string',
            'status' => 'required',
        ]);

        $purchaseOrder->update([
            'supplier' => $request->supplier,
            'amount' => $request->amount,
            'order_date' => $request->order_date,
            'expected_delivery' => $request->expected_delivery,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('supplier-bills.index', ['tab' => 'pos']);
    }

    public function approve($id)
    {
        $po = PurchaseOrder::findOrFail($id);
        $po->update(['status' => 'Approved']);

        return redirect()->route('supplier-bills.index', ['tab' => 'pos']);
    }

    public function receive($id)
    {
        $po = PurchaseOrder::findOrFail($id);

        $grnNextId = GoodsReceivedNote::count() + 1;
        $grn = GoodsReceivedNote::create([
            'grn_no' => 'GRN-' . date('Y') . '-' . str_pad($grnNextId, 3, '0', STR_PAD_LEFT),
            'purchase_order_id' => $po->id,
            'item_name' => $po->item_name,
            'qty_ordered' => $po->qty,
            'qty_received' => $po->qty,
            'supplier' => $po->supplier,
            'amount' => $po->amount,
            'received_date' => today(),
            'status' => 'Pending',
            'notes' => "Received for PO: {$po->po_no}",
        ]);

        $po->update(['status' => 'Received']);

        $billNextId = SupplierBill::count() + 1;
        SupplierBill::create([
            'bill_no' => 'BILL-' . str_pad($billNextId, 2, '0', STR_PAD_LEFT),
            'po_no'   => $po->po_no,
            'grn_no'  => $grn->grn_no,
            'supplier' => $po->supplier,
            'amount' => $po->amount,
            'due_date' => now()->addDays(30),
            'status' => 'Pending',
        ]);

        return redirect()->route('supplier-bills.index', ['tab' => 'grns'])
            ->with('success', "GRN and Bill created for PO {$po->po_no}. Complete the GRN to finalize.");
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();
        return redirect()->route('supplier-bills.index', ['tab' => 'pos']);
    }
}
