<?php namespace App\Services;

use App\Models\AccountPayable\Payment;
use App\Models\AccountPayable\SupplierBill;
use App\Models\FinancialReporting\BudgetVsActual;
use App\Models\FinancialReporting\ComputedFinancialReport;
use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\GeneralLedger\JournalEntryLine;
use App\Models\Sales\SalesTransaction;
use Carbon\Carbon;
use DB;

class FinancialReportService
{
    public function getPeriods(): array
    {
        $dates = collect();

        JournalEntry::where('status', 'Posted')->pluck('transaction_date')->each(fn ($d) => $dates->push($d));
        SupplierBill::whereNotNull('paid_at')->pluck('paid_at')->each(fn ($d) => $dates->push($d));
        Payment::pluck('payment_date')->each(fn ($d) => $dates->push($d));
        SalesTransaction::pluck('created_at')->each(fn ($d) => $dates->push($d));
        BudgetVsActual::pluck('report_period_start')->each(fn ($d) => $dates->push($d));

        return $dates
            ->map(fn ($d) => $d instanceof Carbon ? $d : Carbon::parse($d))
            ->map(fn ($d) => $d->format('F Y'))
            ->unique()
            ->sortBy(fn ($p) => Carbon::parse('first day of ' . $p))
            ->reverse()
            ->values()
            ->toArray();
    }

    public function parsePeriod(?string $period): array
    {
        if (!$period) return [null, null];
        $start = Carbon::parse('first day of ' . $period);
        $end = Carbon::parse('last day of ' . $period);
        return [$start, $end];
    }

    public function computeAndStoreIncomeData(?string $selectedPeriod = null): array
    {
        [$start, $end] = $this->parsePeriod($selectedPeriod);

        $revenue = ChartOfAccount::where('type', 'Revenue')->orderBy('account_name')->get()
            ->map(function ($a) use ($start, $end) {
                $totals = JournalEntryLine::select(DB::raw('COALESCE(SUM(credit),0) as total'))
                    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
                    ->where('journal_entry_lines.account_id', $a->account_id)
                    ->where('journal_entries.status', 'Posted')
                    ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
                    ->first();
                return ['label' => $a->account_name, 'amount' => (float) $totals->total];
            })->filter(fn ($r) => $r['amount'] > 0)->values()->toArray();

        $expenses = ChartOfAccount::where('type', 'Expense')->orderBy('account_name')->get()
            ->map(function ($a) use ($start, $end) {
                $totals = JournalEntryLine::select(DB::raw('COALESCE(SUM(debit),0) as total'))
                    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
                    ->where('journal_entry_lines.account_id', $a->account_id)
                    ->where('journal_entries.status', 'Posted')
                    ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
                    ->first();
                return ['label' => $a->account_name, 'amount' => (float) $totals->total];
            })->filter(fn ($r) => $r['amount'] > 0)->values()->toArray();

        $trialBalance = ChartOfAccount::orderBy('account_name')->get()
            ->map(function ($a) use ($start, $end) {
                $totals = JournalEntryLine::select(
                        DB::raw('COALESCE(SUM(debit),0) as debit_total'),
                        DB::raw('COALESCE(SUM(credit),0) as credit_total'))
                    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
                    ->where('journal_entry_lines.account_id', $a->account_id)
                    ->where('journal_entries.status', 'Posted')
                    ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
                    ->first();
                return [
                    'account' => $a->account_name,
                    'debit'   => (float) $totals->debit_total,
                    'credit'  => (float) $totals->credit_total,
                ];
            })->toArray();

        $this->storeReport('income_statement', $start, $end, $revenue, 'Revenue');
        $this->storeReport('income_statement', $start, $end, $expenses, 'Expense');
        $this->storeReport('trial_balance', $start, $end, $trialBalance);

        return compact('revenue', 'expenses', 'trialBalance');
    }

    public function computeAndStoreAssetsData(?string $selectedPeriod = null): array
    {
        [$start, $end] = $this->parsePeriod($selectedPeriod);

        $assets = []; $liabilities = []; $equity = [];

        $bsAccounts = ChartOfAccount::whereIn('type', ['Asset', 'Liability', 'Equity'])
            ->orderBy('type')->orderBy('account_name')->get();

        foreach ($bsAccounts as $a) {
            $totals = JournalEntryLine::select(
                    DB::raw('COALESCE(SUM(debit),0) as debit_total'),
                    DB::raw('COALESCE(SUM(credit),0) as credit_total'))
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
                ->where('journal_entry_lines.account_id', $a->account_id)
                ->where('journal_entries.status', 'Posted')
                ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
                ->first();

            $balance = $a->normal_balance === 'Credit'
                ? (float) $totals->credit_total - (float) $totals->debit_total
                : (float) $totals->debit_total - (float) $totals->credit_total;

            $item = ['label' => $a->account_name, 'amount' => max($balance, 0)];
            match ($a->type) {
                'Asset'     => $assets[] = $item,
                'Liability' => $liabilities[] = $item,
                'Equity'    => $equity[] = $item,
            };
        }

        $totalRevenue = (float) JournalEntryLine::join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.account_id')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
            ->where('chart_of_accounts.type', 'Revenue')
            ->where('journal_entries.status', 'Posted')
            ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
            ->sum('credit');

        $totalExpenses = (float) JournalEntryLine::join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.account_id')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
            ->where('chart_of_accounts.type', 'Expense')
            ->where('journal_entries.status', 'Posted')
            ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
            ->sum('debit');

        $netIncome = $totalRevenue - $totalExpenses;

        if ($netIncome > 0) {
            $equity[] = ['label' => 'Retained Earnings', 'amount' => $netIncome];
        } elseif ($netIncome < 0) {
            $equity[] = ['label' => 'Retained Earnings (Deficit)', 'amount' => abs($netIncome)];
        }

        $this->storeReport('balance_sheet', $start, $end, $assets, 'Asset');
        $this->storeReport('balance_sheet', $start, $end, $liabilities, 'Liability');
        $this->storeReport('balance_sheet', $start, $end, $equity, 'Equity');

        return compact('assets', 'liabilities', 'equity', 'netIncome');
    }

    public function computeAndStoreCashflowData(?string $selectedPeriod = null): array
    {
        [$start, $end] = $this->parsePeriod($selectedPeriod);

        $cashAccountIds = ChartOfAccount::where('account_name', 'like', 'Cash%')->pluck('account_id');

        $cashInLines = collect();
        if ($cashAccountIds->isNotEmpty()) {
            $cashInLines = JournalEntryLine::selectRaw('coa.account_name, SUM(jel.debit) as total')
                ->from('journal_entry_lines as jel')
                ->join('chart_of_accounts as coa', 'jel.account_id', '=', 'coa.account_id')
                ->join('journal_entries as je', 'jel.journal_entry_id', '=', 'je.journal_entry_id')
                ->whereIn('jel.account_id', $cashAccountIds)
                ->where('jel.debit', '>', 0)
                ->where('je.status', 'Posted')
                ->when($start && $end, fn ($q) => $q->whereBetween('je.transaction_date', [$start, $end]))
                ->whereExists(function ($q) use ($cashAccountIds) {
                    $q->selectRaw(1)
                      ->from('journal_entry_lines as jel2')
                      ->whereColumn('jel2.journal_entry_id', 'jel.journal_entry_id')
                      ->whereNotIn('jel2.account_id', $cashAccountIds);
                })
                ->groupBy('coa.account_name')
                ->get()
                ->map(fn ($r) => ['label' => $r->account_name . ' (received)', 'amount' => (float) $r->total]);
        }

        $cashOutLines = collect();
        if ($cashAccountIds->isNotEmpty()) {
            $cashOutLines = JournalEntryLine::selectRaw('coa.account_name, SUM(jel.credit) as total')
                ->from('journal_entry_lines as jel')
                ->join('chart_of_accounts as coa', 'jel.account_id', '=', 'coa.account_id')
                ->join('journal_entries as je', 'jel.journal_entry_id', '=', 'je.journal_entry_id')
                ->whereIn('jel.account_id', $cashAccountIds)
                ->where('jel.credit', '>', 0)
                ->where('je.status', 'Posted')
                ->when($start && $end, fn ($q) => $q->whereBetween('je.transaction_date', [$start, $end]))
                ->whereExists(function ($q) use ($cashAccountIds) {
                    $q->selectRaw(1)
                      ->from('journal_entry_lines as jel2')
                      ->join('chart_of_accounts as coa2', 'jel2.account_id', '=', 'coa2.account_id')
                      ->whereColumn('jel2.journal_entry_id', 'jel.journal_entry_id')
                      ->whereNotIn('jel2.account_id', $cashAccountIds)
                      ->whereIn('coa2.type', ['Expense', 'Liability']);
                })
                ->groupBy('coa.account_name')
                ->get()
                ->map(fn ($r) => ['label' => $r->account_name . ' (paid)', 'amount' => (float) $r->total]);
        }

        if ($cashAccountIds->isEmpty()) {
            $paidBills = SupplierBill::where('status', 'Paid')
                ->when($start && $end, fn ($q) => $q->whereBetween('paid_at', [$start, $end]))
                ->get();
            foreach ($paidBills as $bill) {
                $cashOutLines->push(['label' => 'Supplier Payment' . ($bill->po_no ? " ({$bill->po_no})" : ''), 'amount' => (float) $bill->amount]);
            }

            $paidSales = SalesTransaction::where('is_posted_to_finance', true)
                ->when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
                ->get();
            foreach ($paidSales as $s) {
                $cashInLines->push(['label' => 'Sales (' . ($s->payment_method ?? 'Unknown') . ')', 'amount' => (float) $s->total_amount]);
            }
        }

        $cashInLines = $cashInLines->groupBy('label')->map(fn ($g) => [
            'label' => $g->first()['label'], 'amount' => $g->sum('amount'),
        ])->values();
        $cashOutLines = $cashOutLines->groupBy('label')->map(fn ($g) => [
            'label' => $g->first()['label'], 'amount' => $g->sum('amount'),
        ])->values();

        $totalCashIn  = $cashInLines->sum('amount');
        $totalCashOut = $cashOutLines->sum('amount');
        $netCashFlow  = $totalCashIn - $totalCashOut;

        $beginningCash = 0;
        if ($start && $cashAccountIds->isNotEmpty()) {
            $beginningCash = (float) JournalEntryLine::whereIn('account_id', $cashAccountIds)
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
                ->where('journal_entries.status', 'Posted')
                ->where('journal_entries.transaction_date', '<', $start)
                ->selectRaw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) as balance')
                ->value('balance');
        }

        $endingCash = $beginningCash + $netCashFlow;

        $allCashLines = collect()
            ->merge($cashInLines->map(fn ($i) => ['label' => $i['label'], 'amount' => $i['amount'], 'section' => 'cash_in']))
            ->merge($cashOutLines->map(fn ($i) => ['label' => $i['label'], 'amount' => $i['amount'], 'section' => 'cash_out']));

        $this->storeReport('cash_flow', $start, $end, $allCashLines->toArray());

        return compact('cashInLines', 'cashOutLines', 'totalCashIn', 'totalCashOut', 'netCashFlow', 'beginningCash', 'endingCash');
    }

    public function getStoredReport(string $reportType, ?string $period = null)
    {
        [$start, $end] = $this->parsePeriod($period);

        $query = ComputedFinancialReport::where('report_type', $reportType);

        if ($start && $end) {
            $query->where('period_start', $start)->where('period_end', $end);
        }

        $records = $query->orderBy('sort_order')->orderBy('id')->get();

        if ($records->isEmpty()) return null;

        return $records;
    }

    public function hasStoredReport(string $reportType, ?string $period = null): bool
    {
        [$start, $end] = $this->parsePeriod($period);

        $query = ComputedFinancialReport::where('report_type', $reportType);
        if ($start && $end) {
            $query->where('period_start', $start)->where('period_end', $end);
        }

        return $query->exists();
    }

    private function storeReport(string $reportType, $start, $end, array $lines, ?string $section = null): void
    {
        $now = now();

        ComputedFinancialReport::where('report_type', $reportType)
            ->where('period_start', $start)
            ->where('period_end', $end)
            ->delete();

        $rows = [];
        foreach ($lines as $i => $line) {
            $rows[] = [
                'report_type'  => $reportType,
                'period_start' => $start,
                'period_end'   => $end,
                'label'        => $line['label'] ?? $line['account'] ?? '',
                'section'      => $line['section'] ?? $section,
                'amount'       => $line['amount'] ?? 0,
                'debit'        => $line['debit'] ?? 0,
                'credit'       => $line['credit'] ?? 0,
                'sort_order'   => $i,
                'generated_at' => $now,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        if (!empty($rows)) {
            ComputedFinancialReport::insert($rows);
        }
    }
}
