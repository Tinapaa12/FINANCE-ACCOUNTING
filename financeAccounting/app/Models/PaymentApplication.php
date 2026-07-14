<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentApplication extends Model
{
    protected $fillable = [
        'payment_id', 'invoice_id', 'amount_applied',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
