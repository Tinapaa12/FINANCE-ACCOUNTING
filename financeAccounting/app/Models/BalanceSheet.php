<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceSheet extends Model
{
    protected $primaryKey = 'balance_sheet_id';

    protected $fillable = ['report_id', 'statement_title', 'period_label', 'generated_at'];

    protected $casts = ['generated_at' => 'datetime'];

    public function report()
    {
        return $this->belongsTo(FinancialReport::class, 'report_id', 'report_id');
    }

    public function lines()
    {
        return $this->hasMany(BalanceSheetLine::class, 'balance_sheet_id', 'balance_sheet_id')
            ->orderBy('line_order');
    }

    public function assets()
    {
        return $this->lines()->where('section', 'Asset');
    }

    public function liabilities()
    {
        return $this->lines()->where('section', 'Liability');
    }

    public function equity()
    {
        return $this->lines()->where('section', 'Equity');
    }

    // These are computed live from the line items every time they're accessed —
    // no stored total column to keep in sync, so they can never drift.
    public function getTotalAssetsAttribute(): float
    {
        return (float) $this->assets()->sum('amount');
    }

    public function getTotalLiabilitiesAttribute(): float
    {
        return (float) $this->liabilities()->sum('amount');
    }

    public function getTotalEquityAttribute(): float
    {
        return (float) $this->equity()->sum('amount');
    }

    public function getTotalLiabilitiesAndEquityAttribute(): float
    {
        return $this->total_liabilities + $this->total_equity;
    }

    // True when Assets == Liabilities + Equity, i.e. the balance sheet actually balances.
    public function getIsBalancedAttribute(): bool
    {
        return round($this->total_assets, 2) === round($this->total_liabilities_and_equity, 2);
    }
}
