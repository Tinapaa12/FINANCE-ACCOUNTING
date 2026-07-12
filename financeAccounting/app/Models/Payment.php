<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'supplier_bill_id',
        'amount',
        'payment_method',
        'payment_date',
        'reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
        ];
    }

    public function supplierBill()
    {
        return $this->belongsTo(SupplierBill::class);
    }
}
