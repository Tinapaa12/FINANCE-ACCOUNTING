<?php // IncomeStatementLine model — a single line entry within an IncomeStatement. Stores line name, amount, and type.
namespace App\Models\FinancialReporting;

use Illuminate\Database\Eloquent\Model;

class IncomeStatementLine extends Model
{
    protected $primaryKey = 'income_statement_line_id';

    protected $fillable = [
        'income_statement_id', 'account_id', 'line_name', 'category', 'amount', 'line_order',
        'report_period_start', 'report_period_end',
    ];

    public function incomeStatement()
    {
        return $this->belongsTo(IncomeStatement::class, 'income_statement_id', 'income_statement_id');
    }

    // Whenever a line is added, edited, or removed, keep the parent
    // Income Statement's total_revenue / total_expenses in sync automatically.
    protected static function booted()
    {
        static::saved(function (self $line) {
            $line->incomeStatement?->recalculateTotals();
        });

        static::deleted(function (self $line) {
            $line->incomeStatement?->recalculateTotals();
        });
    }
}
