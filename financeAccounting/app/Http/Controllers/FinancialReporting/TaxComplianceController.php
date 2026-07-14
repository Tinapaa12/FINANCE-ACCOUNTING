<?php // TaxComplianceController — displays tax compliance records and a PDF export. Shows tax types, taxable amounts, rates, computed tax, and filing status.
namespace App\Http\Controllers\FinancialReporting;

use App\Http\Controllers\Controller;
use App\Models\TaxRecord;

class TaxComplianceController extends Controller
{
    public function index()
    {
        $data = $this->taxData();

        return view('financial-reporting.tax.compliance', $data);
    }

    public function pdf()
    {
        return view('financial-reporting.pdf.tax-compliance', $this->taxData());
    }

    private function taxData(): array
    {
        $periods = TaxRecord::select('tax_period')->distinct()->orderByDesc('tax_period')->pluck('tax_period');
        $selectedPeriod = request('period', $periods->first() ?? 'July 2026');

        $records = TaxRecord::where('tax_period', $selectedPeriod)->get();

        $taxRecords = $records->map(fn ($r) => [
            'reference_type' => $r->reference_type,
            'reference_id'   => $r->reference_id,
            'tax_type'       => $r->tax_type,
            'taxable_amount' => (float) $r->taxable_amount,
            'tax_rate'       => (float) $r->tax_rate,
            'tax_amount'     => (float) $r->tax_amount,
            'filing_status'  => $r->filing_status,
        ])->toArray();

        return [
            'periods'    => $periods,
            'period'     => $selectedPeriod,
            'taxRecords' => $taxRecords,
            'summary'    => [
                'total_taxable' => $records->sum('taxable_amount'),
                'total_tax'     => $records->sum('tax_amount'),
                'total_filed'   => $records->whereIn('filing_status', ['filed', 'paid'])->sum('tax_amount'),
            ],
        ];
    }
}
