<?php
namespace App\Http\Controllers\FinancialReporting;

use App\Http\Controllers\Controller;
use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\GeneralLedger\JournalEntryLine;
use App\Models\FinancialReporting\TaxRecord;
use App\Models\Invoice;
use App\Models\AccountPayable\PurchaseOrder;
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
        $jePeriods = JournalEntry::where('status', 'Posted')
            ->get()
            ->groupBy(fn ($e) => $e->transaction_date->format('F Y'))
            ->keys();
        $poPeriods = PurchaseOrder::whereNotNull('order_date')
            ->get()
            ->groupBy(fn ($e) => $e->order_date->format('F Y'))
            ->keys();
        $invPeriods = Invoice::whereNotNull('invoice_date')
            ->get()
            ->groupBy(fn ($e) => $e->invoice_date->format('F Y'))
            ->keys();
        $periods = $jePeriods->merge($poPeriods)->merge($invPeriods)->unique()->sortDesc()->values();

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

        // 3. AP tax data from Purchase Orders
        $pos = PurchaseOrder::whereBetween('order_date', [$start, $end])
            ->whereIn('status', ['Approved', 'Sent', 'Confirmed', 'Delivered'])
            ->get();
        foreach ($pos as $po) {
            $taxRecords[] = [
                'reference_type' => 'Purchase Order',
                'reference_id'   => $po->id,
                'tax_type'       => 'VAT',
                'taxable_amount' => (float) $po->amount,
                'tax_rate'       => 12,
                'tax_amount'     => round((float) $po->amount * 0.12, 2),
                'filing_status'  => in_array($po->status, ['Delivered', 'Confirmed']) ? 'filed' : 'pending',
            ];
        }

        // 4. AR tax data from Customer Invoices
        $invoices = Invoice::whereBetween('invoice_date', [$start, $end])->get();
        foreach ($invoices as $inv) {
            $taxRecords[] = [
                'reference_type' => 'Customer Invoice',
                'reference_id'   => $inv->id,
                'tax_type'       => 'VAT',
                'taxable_amount' => (float) ($inv->subtotal ?: $inv->total),
                'tax_rate'       => $inv->subtotal > 0 ? round((float) $inv->vat_amount / (float) $inv->subtotal * 100, 2) : 12,
                'tax_amount'     => (float) ($inv->vat_amount ?: round((float) $inv->total * 0.12 / 1.12, 2)),
                'filing_status'  => $inv->status === 'Paid' ? 'filed' : 'pending',
            ];
        }

        // 5. Sync computed data to tax_records table
        TaxRecord::where('tax_period', $selectedPeriod)->delete();
        foreach ($taxRecords as $tr) {
            TaxRecord::create([
                'reference_type' => $tr['reference_type'],
                'reference_id'   => $tr['reference_id'],
                'tax_type'       => $tr['tax_type'],
                'taxable_amount' => $tr['taxable_amount'],
                'tax_rate'       => $tr['tax_rate'],
                'tax_amount'     => $tr['tax_amount'],
                'tax_period'     => $selectedPeriod,
                'filing_status'  => $tr['filing_status'],
            ]);
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
