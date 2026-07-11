<?php // CashFlowReportLine model — a single line entry within a CashFlowReport. Stores line name, amount, and activity type.
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlowReportLine extends Model
{
    protected $primaryKey = 'cash_flow_line_id';

    protected $fillable = ['cash_flow_id', 'activity_type', 'line_name', 'amount', 'line_order'];

    public function cashFlowReport()
    {
        return $this->belongsTo(CashFlowReport::class, 'cash_flow_id', 'cash_flow_id');
    }
}