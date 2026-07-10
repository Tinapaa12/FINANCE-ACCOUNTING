<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRecord extends Model
{
    protected $primaryKey = 'tax_record_id';

    protected $fillable = [
        'reference_type', 'reference_id', 'tax_type', 'taxable_amount',
        'tax_rate', 'tax_amount', 'tax_period', 'filing_status',
    ];

    protected static function booted()
    {
        static::saving(function (self $row) {
            // Auto-compute tax_amount if not explicitly set
            if (empty($row->tax_amount)) {
                $row->tax_amount = round($row->taxable_amount * ($row->tax_rate / 100), 2);
            }
        });
    }
}
