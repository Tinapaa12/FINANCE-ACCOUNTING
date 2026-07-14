<?php // BudgetVsActual model — stores budget vs actual comparison data. Tracks budget/actual amounts with variance status.
namespace App\Models\FinancialReporting;

use Illuminate\Database\Eloquent\Model;

class BudgetVsActual extends Model
{
    protected $primaryKey = 'budget_actual_id';

    protected $fillable = [
        'account_id', 'account_name', 'report_period_start', 'report_period_end',
        'budget_amount', 'actual_amount', 'variance_amount', 'status',
    ];

    protected $casts = [
        'report_period_start' => 'date',
        'report_period_end'   => 'date',
    ];

    // Auto-compute variance and status on save so callers don't have to do it manually
    protected static function booted()
    {
        static::saving(function (self $row) {
            $row->variance_amount = $row->actual_amount - $row->budget_amount;

            if ($row->variance_amount > 0) {
                $row->status = 'Over Budget';
            } elseif ($row->variance_amount < 0) {
                $row->status = 'Under Budget';
            } else {
                $row->status = 'On Budget';
            }
        });
    }
}
