<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoodsReceivedNote;
use App\Models\PurchaseOrder;
use App\Models\SupplierBill;

class GoodsReceivedNoteController extends Controller
{
    public function index()
    {
        $grns = GoodsReceivedNote::with('purchaseOrder')->orderBy('created_at', 'desc')->paginate(6);
        $purchaseOrders = PurchaseOrder::whereIn('status', ['Approved', 'Received'])->orderBy('po_no')->get();
        return view('goods-received-notes.index', compact('grns', 'purchaseOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier' => 'required',
            'amount' => 'required|numeric',
            'received_date' => 'required|date',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'notes' => 'nullable|string',
            'status' => 'required',
        ]);

        $nextId = GoodsReceivedNote::count() + 1;

        $grnData = [
            'grn_no' => 'GRN-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT),
            'purchase_order_id' => $request->purchase_order_id,
            'item_name' => $request->item_name,
            'qty_ordered' => $request->qty_ordered,
            'qty_received' => $request->qty_received,
            'supplier' => $request->supplier,
            'amount' => $request->amount,
            'received_date' => $request->received_date,
            'notes' => $request->notes,
            'status' => $request->status,
        ];

        if ($request->purchase_order_id) {
            $po = PurchaseOrder::find($request->purchase_order_id);
            $grnData['item_name'] = $po->item_name;
            $grnData['qty_ordered'] = $po->qty;
            $grnData['qty_received'] = $po->qty;
        }

        $grn = GoodsReceivedNote::create($grnData);

        if ($request->purchase_order_id) {
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
        }

        return redirect()->route('supplier-bills.index', ['tab' => 'grns']);
    }

    public function update(Request $request, GoodsReceivedNote $goodsReceivedNote)
    {
        $request->validate([
            'supplier' => 'required',
            'amount' => 'required|numeric',
            'received_date' => 'required|date',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'notes' => 'nullable|string',
            'status' => 'required',
        ]);

        $goodsReceivedNote->update([
            'purchase_order_id' => $request->purchase_order_id,
            'supplier' => $request->supplier,
            'amount' => $request->amount,
            'received_date' => $request->received_date,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        return redirect()->route('supplier-bills.index', ['tab' => 'grns']);
    }

    public function complete($id)
    {
        $grn = GoodsReceivedNote::findOrFail($id);
        $grn->update(['status' => 'Completed']);

        if ($grn->purchase_order_id) {
            PurchaseOrder::where('id', $grn->purchase_order_id)->update(['status' => 'Received']);
        }

        return redirect()->route('supplier-bills.index', ['tab' => 'bills']);
    }

    public function destroy(GoodsReceivedNote $goodsReceivedNote)
    {
        $goodsReceivedNote->delete();
        return redirect()->route('supplier-bills.index', ['tab' => 'grns']);
    }
}
