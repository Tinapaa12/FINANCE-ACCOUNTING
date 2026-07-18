<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\AccountPayable\PurchaseOrder;
use App\Services\AccountPayableService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(private AccountPayableService $apService) {}

    public function index()
    {
        $purchaseOrders = PurchaseOrder::orderBy('created_at', 'desc')->get();
        return view('Procurement.purchase-orders.index', compact('purchaseOrders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier' => 'required|string|max:255',
            'item_name' => 'nullable|string|max:255',
            'qty' => 'nullable|integer|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'order_date' => 'required|date',
            'expected_delivery' => 'nullable|date',
        ]);

        $po = $this->apService->createPurchaseOrder($data);

        return redirect()->route('procurement.po.index')
            ->with('success', "Purchase Order {$po->po_no} created as Draft.");
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Draft') {
            return back()->with('error', 'Only draft orders can be edited.');
        }

        $data = $request->validate([
            'supplier' => 'required|string|max:255',
            'item_name' => 'nullable|string|max:255',
            'qty' => 'nullable|integer|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'order_date' => 'required|date',
            'expected_delivery' => 'nullable|date',
        ]);

        $this->apService->updatePurchaseOrder($purchaseOrder, $data);

        return redirect()->route('procurement.po.index')
            ->with('success', "Purchase Order {$purchaseOrder->po_no} updated.");
    }

    public function send($id)
    {
        $po = $this->apService->sendPurchaseOrder((int) $id);
        return redirect()->route('procurement.po.index')
            ->with('success', "PO {$po->po_no} sent to supplier.");
    }

    public function confirm($id)
    {
        $po = $this->apService->confirmPurchaseOrder((int) $id);
        return redirect()->route('procurement.po.index')
            ->with('success', "PO {$po->po_no} confirmed.");
    }

    public function markDelivered($id)
    {
        $po = $this->apService->deliverPurchaseOrder((int) $id);
        return redirect()->route('procurement.po.index')
            ->with('success', "PO {$po->po_no} marked as delivered.");
    }

    public function cancel($id)
    {
        $po = $this->apService->cancelPurchaseOrder((int) $id);
        return redirect()->route('procurement.po.index')
            ->with('success', "PO {$po->po_no} cancelled.");
    }
}
