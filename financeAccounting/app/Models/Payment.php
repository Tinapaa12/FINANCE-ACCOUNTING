<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'customer_id', 'reference_no', 'payment_date', 'method', 'amount', 'notes', 'status',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function applications()
    {
        return $this->hasMany(PaymentApplication::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'payment_applications')
            ->withPivot('amount_applied');
    }
}
