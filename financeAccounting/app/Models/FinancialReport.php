<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialReport extends Model
{
    protected $primaryKey = 'report_id';

    protected $fillable = [
        'report_type', 'report_period_start', 'report_period_end', 'generated_at',
    ];

    protected $casts = [
        'report_period_start' => 'date',
        'report_period_end'   => 'date',
        'generated_at'        => 'datetime',
    ];

    public function incomeStatement()
    {
        return $this->hasOne(IncomeStatement::class, 'report_id', 'report_id');
    }

    public function trialBalanceLines()
    {
        return $this->hasMany(TrialBalance::class, 'report_id', 'report_id');
    }

    public function balanceSheet()
    {
        return $this->hasOne(BalanceSheet::class, 'report_id', 'report_id');
    }

    public function cashFlowReport()
    {
        return $this->hasOne(CashFlowReport::class, 'report_id', 'report_id');
    }
}
