<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'supplier_bill_id',
        'amount',
        'payment_date',
        'method',
        'reference',
        'status',
    ];
}
