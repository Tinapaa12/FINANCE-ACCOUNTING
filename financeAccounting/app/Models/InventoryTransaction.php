<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'inventory_id',
        'type',
        'qty',
        'unit_price',
        'reference',
        'notes',
        'transaction_date',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
        ];
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
