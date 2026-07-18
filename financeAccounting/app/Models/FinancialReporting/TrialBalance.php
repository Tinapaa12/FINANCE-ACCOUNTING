<?php // TrialBalance model — represents a trial balance line linked to a FinancialReport. Stores account-level debit/credit amounts.
namespace App\Models\FinancialReporting;

use Illuminate\Database\Eloquent\Model;

class TrialBalance extends Model
{
    protected $primaryKey = 'trial_balance_id';

    protected $fillable = [
        'report_id', 'account_id', 'account_name', 'debit_amount', 'credit_amount', 'line_order',
    ];

    public function report()
    {
        return $this->belongsTo(FinancialReport::class, 'report_id', 'report_id');
    }
}
