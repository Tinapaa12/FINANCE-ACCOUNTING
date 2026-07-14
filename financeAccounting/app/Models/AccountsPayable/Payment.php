<?php // Payment model — represents a payment made against a supplier bill. Tracks amount paid, date, method, reference, and notes.
namespace App\Models\AccountsPayable;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'supplier_bill_id',
        'amount',
        'payment_method',
        'payment_date',
        'method',
        'reference',
        'status',
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
