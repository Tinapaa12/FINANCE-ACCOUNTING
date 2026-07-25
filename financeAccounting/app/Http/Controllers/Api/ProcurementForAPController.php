<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccountPayable\GoodsReceivedNote;
use App\Models\AccountPayable\PurchaseOrder;
use App\Models\AccountPayable\SupplierBill;

class ProcurementForAPController extends Controller
{
    public function pendingBills()
    {
        return SupplierBill::whereIn('status', ['Pending', 'Approved'])
            ->orderBy('due_date')
            ->get(['id', 'bill_no', 'supplier', 'amount', 'due_date', 'status']);
    }

    public function purchaseOrders()
    {
        return PurchaseOrder::orderBy('created_at', 'desc')
            ->get(['id', 'po_no', 'supplier', 'item_name', 'amount', 'status']);
    }

    public function purchaseOrder($poNo)
    {
        return PurchaseOrder::where('po_no', $poNo)->firstOrFail();
    }

    public function goodsReceipts()
    {
        return GoodsReceivedNote::with('purchaseOrder')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'grn_no', 'supplier', 'item_name', 'amount', 'status', 'purchase_order_id']);
    }

    public function goodsReceipt($grnNo)
    {
        return GoodsReceivedNote::where('grn_no', $grnNo)->with('purchaseOrder')->firstOrFail();
    }
}
