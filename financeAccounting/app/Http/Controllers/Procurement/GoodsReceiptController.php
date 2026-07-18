<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\AccountPayable\PurchaseOrder;
use App\Models\AccountPayable\GoodsReceivedNote;
use App\Services\AccountPayableService;
use Illuminate\Http\Request;

class GoodsReceiptController extends Controller
{
    public function __construct(private AccountPayableService $apService) {}

    public function index()
    {
        $grns = GoodsReceivedNote::with('purchaseOrder')->orderBy('created_at', 'desc')->get();
        return view('Procurement.goods-receipts.index', compact('grns'));
    }

    public function create()
    {
        $purchaseOrders = PurchaseOrder::whereIn('status', ['Confirmed', 'Delivered'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('Procurement.goods-receipts.create', compact('purchaseOrders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'qty_received' => 'required|integer|min:1',
            'received_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        if (!empty($data['purchase_order_id'])) {
            $po = PurchaseOrder::findOrFail($data['purchase_order_id']);
            $data['supplier'] = $po->supplier;
            $data['item_name'] = $po->item_name;
            $data['po_no_ref'] = $po->po_no;
            $data['amount'] = $po->amount;
            $data['qty_ordered'] = $po->qty;
        }

        $grn = $this->apService->createGrn($data);

        if ($grn->purchase_order_id) {
            $po = PurchaseOrder::find($grn->purchase_order_id);
            if ($po && $po->status !== 'Delivered') {
                $this->apService->deliverPurchaseOrder($po->id);
            }
        }

        return redirect()->route('procurement.gr.index')
            ->with('success', "Goods Receipt {$grn->grn_no} recorded successfully.");
    }

    public function complete($id)
    {
        $grn = $this->apService->completeGrn((int) $id);
        return redirect()->route('procurement.gr.index')
            ->with('success', "Goods Receipt {$grn->grn_no} completed.");
    }
}
