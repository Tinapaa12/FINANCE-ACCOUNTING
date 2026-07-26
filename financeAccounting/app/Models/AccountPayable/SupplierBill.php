<?php

namespace App\Models\AccountPayable;

use App\Models\GeneralLedger\ChartOfAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierBill extends Model
{
    protected $table = 'supplier_bills';

    protected $fillable = [
        'bill_no', 'po_no', 'grn_no', 'stock_request_no', 'supplier', 'amount', 'total_paid',
        'due_date', 'status', 'matching_status', 'matching_notes',
        'payment_method', 'paid_at',
        'approved_at', 'approved_by', 'ewt_rate', 'payment_terms', 'expense_account_id',
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

    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'expense_account_id', 'account_id');
    }

    public function getBalanceAttribute()
    {
        return $this->amount - $this->total_paid;
    }
}
