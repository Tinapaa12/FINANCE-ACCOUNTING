<?php // CashFlowReport model — represents a cash flow statement within a FinancialReport. Has many lines split into operating/investing/financing.
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlowReport extends Model
{
    protected $primaryKey = 'cash_flow_id';

    protected $fillable = ['report_id', 'statement_title', 'period_label', 'generated_at'];

    protected $casts = ['generated_at' => 'datetime'];

    public function report()
    {
        return $this->belongsTo(FinancialReport::class, 'report_id', 'report_id');
    }

    public function lines()
    {
        return $this->hasMany(CashFlowReportLine::class, 'cash_flow_id', 'cash_flow_id')
            ->orderBy('line_order');
    }

    public function operatingLines()
    {
        return $this->lines()->where('activity_type', 'Operating');
    }

    public function investingLines()
    {
        return $this->lines()->where('activity_type', 'Investing');
    }

    public function financingLines()
    {
        return $this->lines()->where('activity_type', 'Financing');
    }

    // All computed live from line items — no stored total column, so nothing can drift.
    public function getTotalOperatingAttribute(): float
    {
        return (float) $this->operatingLines()->sum('amount');
    }

    public function getTotalInvestingAttribute(): float
    {
        return (float) $this->investingLines()->sum('amount');
    }

    public function getTotalFinancingAttribute(): float
    {
        return (float) $this->financingLines()->sum('amount');
    }

    public function getNetCashFlowAttribute(): float
    {
        return $this->total_operating + $this->total_investing + $this->total_financing;
    }
}