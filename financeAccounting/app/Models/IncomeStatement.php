<?php // IncomeStatement model — represents an income statement within a FinancialReport. Has many lines split into revenue/expense categories.
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeStatement extends Model
{
    protected $primaryKey = 'income_statement_id';

    protected $fillable = ['report_id', 'total_revenue', 'total_expenses'];

    public function report()
    {
        return $this->belongsTo(FinancialReport::class, 'report_id', 'report_id');
    }

    public function lines()
    {
        return $this->hasMany(IncomeStatementLine::class, 'income_statement_id', 'income_statement_id')
            ->orderBy('line_order');
    }

    public function revenueLines()
    {
        return $this->lines()->where('category', 'revenue');
    }

    public function expenseLines()
    {
        return $this->lines()->where('category', 'expense');
    }

    // Recomputes total_revenue and total_expenses from the actual line items
    // and saves them. Called automatically whenever a line is saved/deleted
    // (see IncomeStatementLine::booted()), so these columns never drift out
    // of sync — you never have to update them by hand.
    public function recalculateTotals(): void
    {
        $this->total_revenue = $this->revenueLines()->sum('amount');
        $this->total_expenses = $this->expenseLines()->sum('amount');
        $this->saveQuietly(); // saveQuietly = save without re-firing model events
    }

    // Convenience accessor: $incomeStatement->net_income
    public function getNetIncomeAttribute(): float
    {
        return (float) $this->total_revenue - (float) $this->total_expenses;
    }
}
