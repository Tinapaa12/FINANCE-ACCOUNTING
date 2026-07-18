<?php

namespace App\Http\Controllers\AccountsPayable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountsPayable\PurchaseOrder;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::orderBy('created_at', 'desc')->paginate(6);
        return view('accounts-payable.purchase-orders.index', compact('purchaseOrders'));
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

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();
        return redirect()->route('supplier-bills.index', ['tab' => 'pos']);
    }
}
