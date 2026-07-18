<?php
namespace App\Http\Controllers\FinancialReporting;

use App\Http\Controllers\Controller;
use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\GeneralLedger\JournalEntryLine;
use App\Models\FinancialReporting\TaxRecord;
use App\Models\Sales\SalesTransaction;
use Carbon\Carbon;

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
        $periods = JournalEntry::where('status', 'Posted')
            ->get()
            ->groupBy(fn ($e) => $e->transaction_date->format('F Y'))
            ->keys()
            ->sortDesc()
            ->values();

        $selectedPeriod = request('period', $periods->first() ?? now()->format('F Y'));
        $start = Carbon::parse('first day of ' . $selectedPeriod);
        $end = Carbon::parse('last day of ' . $selectedPeriod);

        $taxRecords = [];

        // 1. GL tax accounts (VAT, etc.)
        $taxAccountIds = ChartOfAccount::where(function ($q) {
            $q->where('account_name', 'like', '%VAT%')
              ->orWhere('account_name', 'like', '%Tax%');
        })->pluck('account_id');

        if ($taxAccountIds->isNotEmpty()) {
            $lines = JournalEntryLine::with(['journalEntry', 'account'])
                ->whereIn('account_id', $taxAccountIds)
                ->whereHas('journalEntry', fn ($q) => $q->where('status', 'Posted')->whereBetween('transaction_date', [$start, $end]))
                ->get();

            foreach ($lines->groupBy('journal_entry_id') as $jeId => $jeLines) {
                $je = $jeLines->first()->journalEntry;
                $account = $jeLines->first()->account;

                $taxAmount = $account->normal_balance === 'Credit'
                    ? $jeLines->sum('credit') - $jeLines->sum('debit')
                    : $jeLines->sum('debit') - $jeLines->sum('credit');
                $taxAmount = max($taxAmount, 0);
                if ($taxAmount <= 0) continue;

                $otherLines = JournalEntryLine::where('journal_entry_id', $jeId)
                    ->whereNotIn('account_id', $taxAccountIds)
                    ->get();
                $taxableAmount = $account->normal_balance === 'Credit'
                    ? $otherLines->sum('credit')
                    : $otherLines->sum('debit');

                $rate = $taxableAmount > 0 ? round($taxAmount / $taxableAmount * 100, 2) : 0;

                $taxType = 'VAT';
                if (stripos($account->account_name, 'Income Tax') !== false) $taxType = 'Income Tax';

                $taxRecords[] = [
                    'reference_type' => 'Journal Entry',
                    'reference_id'   => $je->journal_entry_id,
                    'tax_type'       => $taxType,
                    'taxable_amount' => $taxableAmount,
                    'tax_rate'       => $rate,
                    'tax_amount'     => $taxAmount,
                    'filing_status'  => 'pending',
                ];
            }
        }

        // 2. AR tax data from SalesTransactions
        $sales = SalesTransaction::whereBetween('created_at', [$start, $end])->get();
        foreach ($sales as $s) {
            $taxRecords[] = [
                'reference_type' => 'Sales Transaction',
                'reference_id'   => $s->sales_transaction_id,
                'tax_type'       => 'VAT',
                'taxable_amount' => (float) $s->total_amount,
                'tax_rate'       => 12,
                'tax_amount'     => round((float) $s->total_amount * 0.12 / 1.12, 2),
                'filing_status'  => $s->is_posted_to_finance ? 'filed' : 'pending',
            ];
        }

        // 3. Merge manual TaxRecord overrides (filing status tracking)
        $manualRecords = TaxRecord::where('tax_period', $selectedPeriod)->get();
        foreach ($manualRecords as $mr) {
            $matched = false;
            foreach ($taxRecords as &$tr) {
                if ($tr['reference_type'] === $mr->reference_type && (string)$tr['reference_id'] === (string)$mr->reference_id) {
                    $tr['filing_status'] = $mr->filing_status;
                    $tr['taxable_amount'] = (float) $mr->taxable_amount;
                    $tr['tax_rate'] = (float) $mr->tax_rate;
                    $tr['tax_amount'] = (float) $mr->tax_amount;
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                $taxRecords[] = [
                    'reference_type' => $mr->reference_type,
                    'reference_id'   => $mr->reference_id,
                    'tax_type'       => $mr->tax_type,
                    'taxable_amount' => (float) $mr->taxable_amount,
                    'tax_rate'       => (float) $mr->tax_rate,
                    'tax_amount'     => (float) $mr->tax_amount,
                    'filing_status'  => $mr->filing_status,
                ];
            }
        }

        return [
            'periods'    => $periods->isNotEmpty() ? $periods : collect([now()->format('F Y')]),
            'period'     => $selectedPeriod,
            'taxRecords' => $taxRecords,
            'summary'    => [
                'total_taxable' => collect($taxRecords)->sum('taxable_amount'),
                'total_tax'     => collect($taxRecords)->sum('tax_amount'),
                'total_filed'   => collect($taxRecords)->whereIn('filing_status', ['filed', 'paid'])->sum('tax_amount'),
            ],
        ];
    }
}
