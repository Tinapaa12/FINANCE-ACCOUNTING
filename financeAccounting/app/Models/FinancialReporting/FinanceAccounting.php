<?php namespace App\Models\FinancialReporting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceAccounting extends Model
{
    protected $table = 'finance_accounting';

    protected $fillable = [
        'report_type', 'period_start', 'period_end',
        'generated_at', 'status', 'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'generated_at' => 'datetime',
    ];

    public function incomeLines(): HasMany
    {
        return $this->hasMany(FinanceAccountingIncome::class, 'finance_accounting_id');
    }

    public function balanceSheetLines(): HasMany
    {
        return $this->hasMany(FinanceAccountingBalanceSheet::class, 'finance_accounting_id');
    }

    public function cashFlowLines(): HasMany
    {
        return $this->hasMany(FinanceAccountingCashFlow::class, 'finance_accounting_id');
    }
}
