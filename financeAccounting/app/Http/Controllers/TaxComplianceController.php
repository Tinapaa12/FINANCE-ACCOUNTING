<?php

namespace App\Http\Controllers;

class TaxComplianceController extends Controller
{
    public function index()
    {
        $taxRecords = [
            [
                'reference_type' => 'Customer Invoice',
                'reference_id' => 1042,
                'tax_type' => 'VAT',
                'taxable_amount' => 520000,
                'tax_rate' => 12,
                'tax_amount' => 62400,
                'filing_status' => 'paid',
            ],
            [
                'reference_type' => 'Supplier Bill',
                'reference_id' => 887,
                'tax_type' => 'VAT',
                'taxable_amount' => 238333,
                'tax_rate' => 12,
                'tax_amount' => 28600,
                'filing_status' => 'paid',
            ],
            [
                'reference_type' => 'Supplier Bill',
                'reference_id' => 891,
                'tax_type' => 'EWT',
                'taxable_amount' => 85000,
                'tax_rate' => 2,
                'tax_amount' => 1700,
                'filing_status' => 'filed',
            ],
            [
                'reference_type' => 'Customer Invoice',
                'reference_id' => 1055,
                'tax_type' => 'VAT',
                'taxable_amount' => 45000,
                'tax_rate' => 12,
                'tax_amount' => 5400,
                'filing_status' => 'pending',
            ],
        ];

        $totalTaxable = collect($taxRecords)->sum('taxable_amount');
        $totalTax = collect($taxRecords)->sum('tax_amount');
        $totalFiled = collect($taxRecords)
            ->whereIn('filing_status', ['filed', 'paid'])
            ->sum('tax_amount');

        return view('tax.compliance', [
            'period' => 'July 2026',
            'taxRecords' => $taxRecords,
            'summary' => [
                'total_taxable' => $totalTaxable,
                'total_tax' => $totalTax,
                'total_filed' => $totalFiled,
            ],
        ]);
    }
}
