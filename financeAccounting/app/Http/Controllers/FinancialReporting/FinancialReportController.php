<?php // FinancialReportController — serves financial report pages and their PDF versions. Provides Income Statement, Balance Sheet, Cash Flow, and Budget vs Actual views with data from generated report models.
namespace App\Http\Controllers\FinancialReporting;

use App\Http\Controllers\Controller;
use App\Models\BudgetVsActual;
use App\Models\FinancialReport;

class FinancialReportController extends Controller
{
    public function income()
    {
        $data = $this->incomeData();

        return view('financial-reporting.reports.income', $data);
    }

    public function incomePdf()
    {
        return view('financial-reporting.pdf.income', $this->incomeData());
    }

    public function assets()
    {
        return view('financial-reporting.reports.assets', $this->assetsData());
    }

    public function assetsPdf()
    {
        return view('financial-reporting.pdf.assets', $this->assetsData());
    }

    public function liabilities()
    {
        return view('financial-reporting.reports.liabilities', $this->liabilitiesData());
    }

    public function liabilitiesPdf()
    {
        return view('financial-reporting.pdf.liabilities', $this->liabilitiesData());
    }

    public function cashflow()
    {
        return view('financial-reporting.reports.cashflow', $this->cashflowData());
    }

    public function cashflowPdf()
    {
        return view('financial-reporting.pdf.cashflow', $this->cashflowData());
    }

    private function incomeData(): array
    {
        $reports = FinancialReport::where('report_type', 'Income Statement')
            ->orderByDesc('report_period_start')
            ->get();

        $reportId = request('report_id');
        $report = $reportId
            ? $reports->firstWhere('report_id', $reportId)
            : $reports->first();

        $start = $report?->report_period_start;
        $end   = $report?->report_period_end;

        $revenue = [];
        $expenses = [];
        $trialBalance = [];

        // All Revenue accounts (including zero-balance)
        $revenue = \App\Models\ChartOfAccount::where('type', 'Revenue')->orderBy('account_name')->get()
            ->map(function ($a) use ($start, $end) {
                $totals = \App\Models\JournalEntryLine::select(
                        \DB::raw('COALESCE(SUM(credit),0) as total'))
                    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
                    ->where('journal_entry_lines.account_id', $a->account_id)
                    ->where('journal_entries.status', 'Posted')
                    ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
                    ->first();
                return ['label' => $a->account_name, 'amount' => (float) $totals->total];
            })->filter(fn ($r) => $r['amount'] > 0)->values()->toArray();

        // All Expense accounts (including zero-balance)
        $expenses = \App\Models\ChartOfAccount::where('type', 'Expense')->orderBy('account_name')->get()
            ->map(function ($a) use ($start, $end) {
                $totals = \App\Models\JournalEntryLine::select(
                        \DB::raw('COALESCE(SUM(debit),0) as total'))
                    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
                    ->where('journal_entry_lines.account_id', $a->account_id)
                    ->where('journal_entries.status', 'Posted')
                    ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
                    ->first();
                return ['label' => $a->account_name, 'amount' => (float) $totals->total];
            })->filter(fn ($r) => $r['amount'] > 0)->values()->toArray();

        // Trial balance — all accounts, even zero-balance
        $trialBalance = \App\Models\ChartOfAccount::orderBy('account_name')->get()
            ->map(function ($a) use ($start, $end) {
                $totals = \App\Models\JournalEntryLine::select(
                        \DB::raw('COALESCE(SUM(debit),0) as debit_total'),
                        \DB::raw('COALESCE(SUM(credit),0) as credit_total'))
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

        return [
            'reports'          => $reports,
            'report'           => $report,
            'selectedReportId' => $report?->report_id,
            'month'            => $report?->report_period_start?->format('F') ?? 'All',
            'revenue'          => $revenue,
            'expenses'         => $expenses,
            'trialBalance'     => $trialBalance,
        ];
    }

    private function assetsData(): array
    {
        $reports = FinancialReport::where('report_type', 'Balance Sheet')
            ->orderByDesc('report_period_start')
            ->get();

        $reportId = request('report_id');
        $report = $reportId
            ? $reports->firstWhere('report_id', $reportId)
            : $reports->first();

        $start = $report?->report_period_start;
        $end   = $report?->report_period_end;

        $assets      = [];
        $liabilities = [];
        $equity      = [];

        // All Asset/Liability/Equity accounts (including zero-balance)
        $bsAccounts = \App\Models\ChartOfAccount::whereIn('type', ['Asset', 'Liability', 'Equity'])
            ->orderBy('type')
            ->orderBy('account_name')
            ->get();

        foreach ($bsAccounts as $a) {
            $totals = \App\Models\JournalEntryLine::select(
                    \DB::raw('COALESCE(SUM(debit),0) - COALESCE(SUM(credit),0) as balance'))
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
                ->where('journal_entry_lines.account_id', $a->account_id)
                ->where('journal_entries.status', 'Posted')
                ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
                ->first();

            $item = ['label' => $a->account_name, 'amount' => (float) abs($totals->balance)];
            match ($a->type) {
                'Asset'     => $assets[] = $item,
                'Liability' => $liabilities[] = $item,
                'Equity'    => $equity[] = $item,
            };
        }

        return [
            'assets'           => $assets,
            'liabilities'      => $liabilities,
            'equity'           => $equity,
            'reports'          => $reports,
            'report'           => $report,
            'selectedReportId' => $report?->report_id,
            'hasData'          => !empty($assets) || !empty($liabilities) || !empty($equity),
        ];
    }

    private function liabilitiesData(): array
    {
        $reports = FinancialReport::orderByDesc('report_period_start')->get();
        $reportId = request('report_id');
        $report = $reportId
            ? $reports->firstWhere('report_id', $reportId)
            : $reports->first();

        $start = $report?->report_period_start;
        $end   = $report?->report_period_end;

        $budgetRows = BudgetVsActual::when($start && $end, fn ($q) => $q
                ->whereBetween('report_period_start', [$start, $end])
                ->orWhereBetween('report_period_end', [$start, $end])
            )
            ->orderBy('budget_actual_id')
            ->get();

        if ($budgetRows->isEmpty()) {
            return [
                'reports'          => $reports,
                'report'           => $report,
                'selectedReportId' => $report?->report_id,
                'budgetVsActual'   => [],
            ];
        }

        // Get actuals from journal entries for the same accounts
        $actuals = \App\Models\JournalEntryLine::select('chart_of_accounts.account_name',
                \DB::raw('SUM(journal_entry_lines.debit) as debit_total'),
                \DB::raw('SUM(journal_entry_lines.credit) as credit_total'))
            ->join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.account_id')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
            ->where('journal_entries.status', 'Posted')
            ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
            ->groupBy('chart_of_accounts.account_name')
            ->get()
            ->keyBy('account_name');

        return [
            'reports'          => $reports,
            'report'           => $report,
            'selectedReportId' => $report?->report_id,
            'budgetVsActual' => $budgetRows->map(function ($row) use ($actuals) {
                $accountName = $row->account_name;
                $budgetAmount = (float) $row->budget_amount;

                // Determine actual: for Revenue accounts use credit total, for Expense use debit total
                $actualAccount = $actuals->get($accountName);
                if ($actualAccount) {
                    $actualAmount = (float) $actualAccount->debit_total + (float) $actualAccount->credit_total;
                } else {
                    $actualAmount = (float) $row->actual_amount;
                }

                $variance = $actualAmount - $budgetAmount;

                return [
                    'account' => $accountName,
                    'budget'  => $budgetAmount,
                    'actual'  => $actualAmount,
                    'status'  => match (true) {
                        $variance > 0 && $variance / max($budgetAmount, 1) < 0.05 => 'slightly_over',
                        $variance > 0 => 'over',
                        $variance < 0 => 'under',
                        default       => 'on_budget',
                    },
                ];
            })->toArray(),
        ];
    }

    private function cashflowData(): array
    {
        $reports = FinancialReport::where('report_type', 'Cash Flow Statement')
            ->orderByDesc('report_period_start')
            ->get();

        $reportId = request('report_id');
        $report = $reportId
            ? $reports->firstWhere('report_id', $reportId)
            : $reports->first();

        $start = $report?->report_period_start;
        $end   = $report?->report_period_end;

        // Cash In = posted journal entries with Revenue-type accounts (credits)
        $cashInLines = \App\Models\JournalEntryLine::select('account_name', \DB::raw('SUM(credit) as total'))
            ->join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.account_id')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
            ->where('chart_of_accounts.type', 'Revenue')
            ->where('journal_entries.status', 'Posted')
            ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
            ->groupBy('account_name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => ['label' => $r->account_name, 'amount' => (float) $r->total])
            ->toArray();

        // Cash Out = posted journal entries with Expense-type accounts (debits)
        $cashOutLines = \App\Models\JournalEntryLine::select('account_name', \DB::raw('SUM(debit) as total'))
            ->join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.account_id')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
            ->where('chart_of_accounts.type', 'Expense')
            ->where('journal_entries.status', 'Posted')
            ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
            ->groupBy('account_name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => ['label' => $r->account_name, 'amount' => (float) $r->total])
            ->toArray();

        $totalCashIn  = array_sum(array_column($cashInLines, 'amount'));
        $totalCashOut = array_sum(array_column($cashOutLines, 'amount'));
        $netCashFlow  = $totalCashIn - $totalCashOut;

        $beginningCash = (float) \App\Models\JournalEntryLine::join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.account_id')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
            ->where('chart_of_accounts.type', 'Asset')
            ->where('journal_entries.status', 'Posted')
            ->when($start && $end, fn ($q) => $q->where('journal_entries.transaction_date', '<', $start))
            ->select(\DB::raw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) as balance'))
            ->value('balance');

        return [
            'reports'          => $reports,
            'selectedReportId' => $report?->report_id,
            'periodLabel'      => $report?->report_period_start?->format('F Y') ?? 'All',
            'cashInLines'      => $cashInLines,
            'cashOutLines'     => $cashOutLines,
            'totalCashIn'      => $totalCashIn,
            'totalCashOut'     => $totalCashOut,
            'netCashFlow'      => $netCashFlow,
            'beginningCash'    => (float) $beginningCash,
            'endingCash'       => (float) $beginningCash + $netCashFlow,
            'hasData'          => true,
        ];
    }
}
