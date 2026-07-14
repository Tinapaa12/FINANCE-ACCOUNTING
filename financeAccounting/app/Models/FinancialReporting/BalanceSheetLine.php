<?php // BalanceSheetLine model — a single line entry within a BalanceSheet. Stores line name, amount, and category.
namespace App\Models\FinancialReporting;

use Illuminate\Database\Eloquent\Model;

class BalanceSheetLine extends Model
{
    protected $primaryKey = 'balance_sheet_line_id';

    protected $fillable = [
        'balance_sheet_id', 'account_id', 'line_name', 'section', 'amount', 'line_order',
    ];

    public function balanceSheet()
    {
        return $this->belongsTo(BalanceSheet::class, 'balance_sheet_id', 'balance_sheet_id');
    }
}
