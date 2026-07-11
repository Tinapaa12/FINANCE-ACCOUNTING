<?php // SupplierBill model — represents a bill from a supplier in Accounts Payable. Tracks bill number, PO/GRN, supplier, amount, due date, and status.
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