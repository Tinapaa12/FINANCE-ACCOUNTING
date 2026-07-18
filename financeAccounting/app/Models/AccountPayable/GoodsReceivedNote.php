<?php

namespace App\Models\AccountPayable;

use Illuminate\Database\Eloquent\Model;

class GoodsReceivedNote extends Model
{
    protected $table = 'goods_received_notes';

    protected $fillable = [
        'grn_no', 'purchase_order_id', 'po_no_ref', 'supplier_bill_id',
        'item_name', 'qty_ordered', 'qty_received',
        'supplier', 'amount', 'received_date', 'notes', 'status',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
        ];
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function attachments()
    {
        return $this->morphMany(\App\Models\Attachment::class, 'attachable');
    }
}
