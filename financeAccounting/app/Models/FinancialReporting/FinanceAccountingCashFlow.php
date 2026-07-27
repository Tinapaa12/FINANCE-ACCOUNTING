<?php namespace App\Models\FinancialReporting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\GeneralLedger\ChartOfAccount;

class FinanceAccountingCashFlow extends Model
{
    protected $table = 'finance_accounting_cash_flow';

    protected $fillable = [
        'finance_accounting_id', 'account_id',
        'label', 'section', 'amount', 'sort_order',
    ];

    public function financeAccounting(): BelongsTo
    {
        return $this->belongsTo(FinanceAccounting::class, 'finance_accounting_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id', 'account_id');
    }
}
