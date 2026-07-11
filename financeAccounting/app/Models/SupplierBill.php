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
    'due_date',
    'status',
];
}