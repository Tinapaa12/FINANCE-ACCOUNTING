<?php namespace App\Models\FinancialReporting;

use Illuminate\Database\Eloquent\Model;

class ComputedFinancialReport extends Model
{
    protected $fillable = [
        'report_type', 'period_start', 'period_end',
        'label', 'section', 'amount', 'debit', 'credit',
        'sort_order', 'generated_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'generated_at' => 'datetime',
    ];
}
