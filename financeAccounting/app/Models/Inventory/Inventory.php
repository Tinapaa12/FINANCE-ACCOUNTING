<?php
namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventories';

    protected $fillable = [
        'item_name', 'sku', 'description', 'quantity', 'unit_cost',
        'selling_price', 'category', 'status',
    ];

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
