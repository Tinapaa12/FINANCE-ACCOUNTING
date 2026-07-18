<?php

namespace App\Models\AccountPayable;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';

    protected $fillable = [
        'po_no', 'supplier', 'item_name', 'qty', 'unit_cost', 'amount',
        'description', 'order_date', 'expected_delivery', 'status',
        'sent_at', 'confirmed_at', 'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'expected_delivery' => 'date',
            'sent_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function attachments()
    {
        return $this->morphMany(\App\Models\Attachment::class, 'attachable');
    }
}
