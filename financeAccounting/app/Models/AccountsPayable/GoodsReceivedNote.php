<?php

namespace App\Models\AccountsPayable;

use Illuminate\Database\Eloquent\Model;

class GoodsReceivedNote extends Model
{
    protected $fillable = [
        'grn_no',
        'purchase_order_id',
        'supplier',
        'amount',
        'received_date',
        'notes',
        'status',
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
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
