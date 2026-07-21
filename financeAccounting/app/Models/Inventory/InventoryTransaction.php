<?php
namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $table = 'inventory_transactions';

    protected $fillable = [
        'inventory_id', 'type', 'quantity', 'unit_cost', 'total',
        'reference_type', 'reference_id', 'notes',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
