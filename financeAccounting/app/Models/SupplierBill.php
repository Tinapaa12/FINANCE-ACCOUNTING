<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierBill extends Model
{
protected $fillable = [
    'bill_no',
    'po_no',
    'grn_no',
    'supplier',
    'amount',
    'total_paid',
    'due_date',
    'status',
    'payment_method',
    'paid_at',
    'approved_at',
    'approved_by',
    'ewt_rate',
    'payment_terms',
];

protected function casts(): array
{
    return [
        'paid_at' => 'datetime',
        'approved_at' => 'datetime',
    ];
}

public function payments()
{
    return $this->hasMany(Payment::class, 'supplier_bill_id');
}

public function attachments()
{
    return $this->morphMany(Attachment::class, 'attachable');
}

public function getBalanceAttribute()
{
    return $this->amount - $this->total_paid;
}
}