<?php // Payment model — represents a payment made against a supplier bill. Tracks amount paid, date, method, reference, and notes.
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
