<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\GoodsReceivedNote;
use App\Models\SupplierBill;
use App\Models\PurchaseOrder;

class InventoryController extends Controller
{
    public function index()
    {
        $items = Inventory::with('transactions')->paginate(10);
        $transactions = InventoryTransaction::with('inventory')->latest()->paginate(20);
        return view('inventory.index', compact('items', 'transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'expiration_date' => 'nullable|date',
        ]);

        $item = Inventory::create($request->only(['item_name', 'quantity', 'price', 'expiration_date']));

        InventoryTransaction::create([
            'inventory_id' => $item->id,
            'type' => 'stock_in',
            'qty' => $request->quantity,
            'unit_price' => $request->price,
            'reference' => 'Initial stock',
            'notes' => 'Item added to inventory',
        ]);

        return redirect()->route('inventory.index')->with('success', 'Item added to inventory.');
    }

    public function stockIn(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'qty' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $item = Inventory::findOrFail($request->inventory_id);
        $item->increment('quantity', $request->qty);

        InventoryTransaction::create([
            'inventory_id' => $item->id,
            'type' => 'stock_in',
            'qty' => $request->qty,
            'unit_price' => $request->unit_price ?? $item->price,
            'reference' => $request->reference,
            'notes' => $request->notes,
        ]);

        return redirect()->route('inventory.index')->with('success', "Stock-in recorded: {$request->qty} units added to {$item->item_name}.");
    }

    public function stockOut(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'qty' => 'required|integer|min:1',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $item = Inventory::findOrFail($request->inventory_id);

        if ($item->quantity < $request->qty) {
            return redirect()->route('inventory.index')->with('error', "Insufficient stock. Only {$item->quantity} units available.");
        }

        $item->decrement('quantity', $request->qty);

        InventoryTransaction::create([
            'inventory_id' => $item->id,
            'type' => 'stock_out',
            'qty' => $request->qty,
            'unit_price' => $item->price,
            'reference' => $request->reference,
            'notes' => $request->notes,
        ]);

        return redirect()->route('inventory.index')->with('success', "Stock-out recorded: {$request->qty} units removed from {$item->item_name}.");
    }

    public function tracking()
    {
        $items = Inventory::all();
        $purchaseOrders = \App\Models\PurchaseOrder::where('status', 'Approved')->orderBy('po_no')->get();
        return view('inventory.tracking', compact('items', 'purchaseOrders'));
    }

    public function trackingReceive(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'qty_received' => 'required|integer|min:1',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'supplier' => 'nullable|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $item = Inventory::findOrFail($request->inventory_id);
        $supplier = $request->supplier ?? 'Unknown Supplier';

        $po = $request->purchase_order_id ? \App\Models\PurchaseOrder::find($request->purchase_order_id) : null;

        if ($po) {
            $qtyOrdered = $po->qty;
            $qtyReceived = $request->qty_received;
            $unitPrice = $po->unit_cost;
            $billPoNo = $po->po_no;
        } else {
            $qtyOrdered = $request->qty_received;
            $qtyReceived = $request->qty_received;
            $unitPrice = $item->price;
            $billPoNo = 'N/A';
        }

        $totalAmount = $unitPrice * $qtyReceived;

        $grnNextId = GoodsReceivedNote::count() + 1;
        $grn = GoodsReceivedNote::create([
            'grn_no' => 'GRN-' . date('Y') . '-' . str_pad($grnNextId, 3, '0', STR_PAD_LEFT),
            'purchase_order_id' => $po?->id,
            'item_name' => $item->item_name,
            'qty_ordered' => $qtyOrdered,
            'qty_received' => $qtyReceived,
            'supplier' => $supplier,
            'amount' => $totalAmount,
            'received_date' => today(),
            'status' => 'Pending',
            'notes' => $request->notes ?: ($request->reference ? "Ref: {$request->reference}" : null),
        ]);

        if ($po) {
            $po->update(['status' => 'Received']);
        }

        $item->increment('quantity', $request->qty_received);

        InventoryTransaction::create([
            'inventory_id' => $item->id,
            'type' => 'stock_in',
            'qty' => $request->qty_received,
            'unit_price' => $unitPrice,
            'reference' => $request->reference,
            'notes' => 'GRN: ' . $grn->grn_no . ($request->notes ? " - {$request->notes}" : ''),
        ]);

        $billNextId = SupplierBill::count() + 1;
        SupplierBill::create([
            'bill_no' => 'BILL-' . str_pad($billNextId, 2, '0', STR_PAD_LEFT),
            'po_no'   => $billPoNo,
            'grn_no'  => $grn->grn_no,
            'supplier' => $supplier,
            'amount' => $totalAmount,
            'due_date' => now()->addDays(30),
            'status' => 'Pending',
        ]);

        return redirect()->route('inventory.tracking')
            ->with('success', "Received {$request->qty_received} units of {$item->item_name}. GRN #{$grn->grn_no} created.");
    }

    public function purchase($id)
    {
        $item = Inventory::findOrFail($id);

        $poNextId = PurchaseOrder::count() + 1;
        $poNo = 'PO-' . date('Y') . '-' . str_pad($poNextId, 3, '0', STR_PAD_LEFT);

        PurchaseOrder::create([
            'po_no' => $poNo,
            'supplier' => 'Inventory Supplier',
            'item_name' => $item->item_name,
            'qty' => $item->quantity,
            'unit_cost' => $item->price,
            'amount' => $item->price * $item->quantity,
            'description' => "Auto-created from inventory: {$item->item_name}",
            'order_date' => today(),
            'expected_delivery' => today()->addDays(7),
            'status' => 'Pending',
        ]);

        return redirect()->route('supplier-bills.index', ['tab' => 'pos'])
            ->with('success', "PO created for {$item->item_name}. Approve the PO, then Receive to create the GRN and Bill.");
    }
}
