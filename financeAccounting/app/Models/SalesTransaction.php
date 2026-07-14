<?php // SalesTransaction model — represents a dummy sales transaction simulating an external ERP Sales module. Stores order info, payment, status, and finance posting flag.
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesTransaction extends Model
{
    protected $primaryKey = 'sales_transaction_id';

    protected $fillable = [
        'order_no',
        'customer_name',
        'total_amount',
        'payment_method',
        'status',
        'is_posted_to_finance',
        'journal_entry_id',
        'invoice_date',
        'due_date',
        'invoice_type',
        'currency',
        'subtotal',
        'vat_amount',
        'line_items',
        'description',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'is_posted_to_finance' => 'boolean',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'line_items' => 'array',
    ];

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id', 'journal_entry_id');
    }
}
