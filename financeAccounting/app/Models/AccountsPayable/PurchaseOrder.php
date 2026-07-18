<?php

namespace App\Models\AccountsPayable;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';

    protected $fillable = [
        'po_no',
        'supplier',
        'item_name',
        'qty',
        'unit_cost',
        'amount',
        'description',
        'order_date',
        'expected_delivery',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'expected_delivery' => 'date',
        ];
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
