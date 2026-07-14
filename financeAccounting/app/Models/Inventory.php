<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'item_name',
        'quantity',
        'price',
        'expiration_date',
    ];

    protected function casts(): array
    {
        return [
            'expiration_date' => 'date',
        ];
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
