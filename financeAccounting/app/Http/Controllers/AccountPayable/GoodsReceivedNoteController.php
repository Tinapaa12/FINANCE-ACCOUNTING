<?php

namespace App\Http\Controllers\AccountPayable;

use App\Http\Controllers\Controller;
use App\Models\AccountPayable\GoodsReceivedNote;
use App\Models\AccountPayable\PurchaseOrder;
use App\Services\AccountPayableService;
use Illuminate\Http\Request;

class GoodsReceivedNoteController extends Controller
{
    public function __construct(
        private readonly AccountPayableService $apService
    ) {}

    public function index()
    {
        $grns = GoodsReceivedNote::with('purchaseOrder')->orderBy('created_at', 'desc')->paginate(6);
        $purchaseOrders = PurchaseOrder::whereIn('status', ['Approved', 'Received'])->orderBy('po_no')->get();
        return view('account-payable::goods-received-notes.index', compact('grns', 'purchaseOrders'));
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
            'qty_received' => 'nullable|integer|min:1',
        ]);

        $qtyReceived = $request->qty_received ?? 1;

        if ($request->purchase_order_id) {
            $po = PurchaseOrder::find($request->purchase_order_id);
            $qtyOrdered = $po->qty;
            $itemName = $po->item_name;
            $amount = $po->amount;
        } else {
            $qtyOrdered = $qtyReceived;
            $itemName = $request->item_name;
            $amount = $request->amount;
        }

        $grn = $this->apService->createGrn([
            'purchase_order_id' => $request->purchase_order_id,
            'item_name' => $itemName,
            'qty_ordered' => $qtyOrdered,
            'qty_received' => $qtyReceived,
            'supplier' => $request->supplier,
            'amount' => $amount,
            'received_date' => $request->received_date,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        if ($request->purchase_order_id) {
            $po = PurchaseOrder::find($request->purchase_order_id);
            $po->update(['status' => 'Received']);

            $this->apService->createBill([
                'po_no' => $po->po_no,
                'grn_no' => $grn->grn_no,
                'supplier' => $po->supplier,
                'amount' => $po->amount,
                'due_date' => now()->addDays(30),
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
        $grn = $this->apService->completeGrn($id);
        return redirect()->route('supplier-bills.index', ['tab' => 'bills']);
    }

    public function destroy(GoodsReceivedNote $goodsReceivedNote)
    {
        $goodsReceivedNote->delete();
        return redirect()->route('supplier-bills.index', ['tab' => 'grns']);
    }
}
