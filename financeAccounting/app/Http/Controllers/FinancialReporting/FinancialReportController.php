<?php // FinancialReportController — serves financial report pages and their PDF versions. Provides Income Statement, Balance Sheet, Cash Flow, and Budget vs Actual views with data from generated report models.
namespace App\Http\Controllers\FinancialReporting;

use App\Http\Controllers\Controller;
use App\Models\BalanceSheet;
use App\Models\BudgetVsActual;
use App\Models\CashFlowReport;
use App\Models\FinancialReport;
use App\Models\IncomeStatement;

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
        $reportId = request('report_id');
        $reports = FinancialReport::where('report_type', 'Income Statement')
            ->orderByDesc('report_period_start')
            ->get();

        $report = $reportId
            ? $reports->firstWhere('report_id', $reportId)
            : $reports->first();

        $revenue = [];
        $expenses = [];
        $trialBalance = [];

        if ($report) {
            $incomeStatement = IncomeStatement::where('report_id', $report->report_id)->first();

            if ($incomeStatement) {
                $revenue = $incomeStatement->revenueLines()->get()
                    ->map(fn ($line) => ['label' => $line->line_name, 'amount' => (float) $line->amount])
                    ->toArray();

                $expenses = $incomeStatement->expenseLines()->get()
                    ->map(fn ($line) => ['label' => $line->line_name, 'amount' => (float) $line->amount])
                    ->toArray();
            }

            $trialBalance = $report->trialBalanceLines()->orderBy('line_order')->get()
                ->map(fn ($row) => [
                    'account' => $row->account_name,
                    'debit'   => $row->debit_amount,
                    'credit'  => $row->credit_amount,
                ])->toArray();
        }

        return [
            'reports'          => $reports,
            'report'           => $report,
            'selectedReportId' => $report?->report_id,
            'month'            => $report?->report_period_start?->format('F') ?? 'N/A',
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

        if (!$report) {
            return ['assets' => [], 'liabilities' => [], 'equity' => [], 'reports' => collect(), 'selectedReportId' => null, 'hasData' => false];
        }

        $assets      = [];
        $liabilities = [];
        $equity      = [];

        $tbRows = \App\Models\TrialBalance::where('report_id', $report->report_id)->count();

        if ($tbRows > 0) {
            $rows = \App\Models\TrialBalance::select('trial_balances.account_name',
                    \DB::raw('trial_balances.debit_amount - trial_balances.credit_amount as balance'),
                    \DB::raw('COALESCE(chart_of_accounts.type, CASE
                        WHEN trial_balances.debit_amount > 0 AND (trial_balances.credit_amount IS NULL OR trial_balances.credit_amount = 0) THEN "Asset"
                        WHEN trial_balances.credit_amount > 0 AND (trial_balances.debit_amount IS NULL OR trial_balances.debit_amount = 0) THEN "Liability"
                        ELSE "Equity"
                    END) as type'))
                ->leftJoin('chart_of_accounts', 'trial_balances.account_id', '=', 'chart_of_accounts.account_id')
                ->where('trial_balances.report_id', $report->report_id)
                ->orderBy('type')
                ->orderByDesc('balance')
                ->get();

            foreach ($rows as $r) {
                $item = ['label' => $r->account_name, 'amount' => (float) abs($r->balance)];
                match ($r->type) {
                    'Asset'     => $assets[] = $item,
                    'Liability' => $liabilities[] = $item,
                    'Equity'    => $equity[] = $item,
                };
            }
        } else {
            // Fallback: check manually added balance sheet lines
            $balanceSheet = \App\Models\BalanceSheet::where('report_id', $report->report_id)->first();
            if ($balanceSheet) {
                foreach ($balanceSheet->assets()->get() as $l) {
                    $assets[] = ['label' => $l->line_name, 'amount' => (float) $l->amount];
                }
                foreach ($balanceSheet->liabilities()->get() as $l) {
                    $liabilities[] = ['label' => $l->line_name, 'amount' => (float) $l->amount];
                }
                foreach ($balanceSheet->equity()->get() as $l) {
                    $equity[] = ['label' => $l->line_name, 'amount' => (float) $l->amount];
                }
            }

            // If still empty, compute from all posted journal entries (cumulative)
            if (empty($assets) && empty($liabilities) && empty($equity)) {
                $rows = \App\Models\JournalEntryLine::select('chart_of_accounts.type', 'chart_of_accounts.account_name',
                        \DB::raw('SUM(journal_entry_lines.debit - journal_entry_lines.credit) as balance'))
                    ->join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.account_id')
                    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
                    ->whereIn('chart_of_accounts.type', ['Asset', 'Liability', 'Equity'])
                    ->where('journal_entries.status', 'Posted')
                    ->groupBy('chart_of_accounts.type', 'chart_of_accounts.account_name')
                    ->orderBy('chart_of_accounts.type')
                    ->orderByDesc('balance')
                    ->get();

                foreach ($rows as $r) {
                    $item = ['label' => $r->account_name, 'amount' => (float) abs($r->balance)];
                    match ($r->type) {
                        'Asset'     => $assets[] = $item,
                        'Liability' => $liabilities[] = $item,
                        'Equity'    => $equity[] = $item,
                    };
                }
            }
        }

        return [
            'assets'           => $assets,
            'liabilities'      => $liabilities,
            'equity'           => $equity,
            'reports'          => $reports,
            'selectedReportId' => $report->report_id,
            'hasData'          => !empty($assets) || !empty($liabilities) || !empty($equity),
        ];
    }

    private function liabilitiesData(): array
    {
        $periods = BudgetVsActual::select('report_period_start')->distinct()->orderByDesc('report_period_start')->get()
            ->map(fn ($r) => $r->report_period_start->format('F Y'))
            ->unique();

        $selectedPeriod = request('period', $periods->first() ?? now()->format('F Y'));

        $start = \Carbon\Carbon::createFromFormat('F Y', $selectedPeriod)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $rows = BudgetVsActual::whereBetween('report_period_start', [$start, $end])
            ->orWhereBetween('report_period_end', [$start, $end])
            ->orderBy('created_at')
            ->get();

        return [
            'periods'    => $periods,
            'period'     => $selectedPeriod,
            'reportDate' => $rows->first()?->report_period_end?->format('F j, Y') ?? '',
            'budgetVsActual' => $rows->map(fn ($row) => [
                'account' => $row->account_name,
                'budget'  => (float) $row->budget_amount,
                'actual'  => (float) $row->actual_amount,
                'status'  => match ($row->status) {
                    'Over Budget'  => (abs($row->variance_amount) / max($row->budget_amount, 1)) < 0.05 ? 'slightly_over' : 'over',
                    'Under Budget' => 'under',
                    default        => 'on_budget',
                },
            ])->toArray(),
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

        if (!$report) {
            return [
                'reports' => $reports, 'selectedReportId' => null,
                'periodLabel' => '', 'cashInLines' => [], 'cashOutLines' => [],
                'totalCashIn' => 0, 'totalCashOut' => 0, 'netCashFlow' => 0,
                'beginningCash' => 0, 'endingCash' => 0, 'hasData' => false,
            ];
        }

        $start = $report->report_period_start;
        $end   = $report->report_period_end;

        // Cash In = posted journal entries with Revenue-type accounts (credits)
        $cashInLines = \App\Models\JournalEntryLine::select('account_name', \DB::raw('SUM(credit) as total'))
            ->join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.account_id')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
            ->where('chart_of_accounts.type', 'Revenue')
            ->where('journal_entries.status', 'Posted')
            ->whereBetween('journal_entries.transaction_date', [$start, $end])
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
            ->whereBetween('journal_entries.transaction_date', [$start, $end])
            ->groupBy('account_name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => ['label' => $r->account_name, 'amount' => (float) $r->total])
            ->toArray();

        // Also include manually added entries from cash_flow_report_lines
        $manualLines = \App\Models\CashFlowReportLine::where('activity_type', 'Cash In')
            ->orWhere('activity_type', 'Cash Out')
            ->get()
            ->map(fn ($l) => ['label' => $l->line_name, 'amount' => (float) $l->amount, 'type' => $l->activity_type]);

        foreach ($manualLines as $ml) {
            if ($ml['type'] === 'Cash In') {
                $cashInLines[] = ['label' => $ml['label'], 'amount' => $ml['amount']];
            } else {
                $cashOutLines[] = ['label' => $ml['label'], 'amount' => abs($ml['amount'])];
            }
        }

        $totalCashIn  = array_sum(array_column($cashInLines, 'amount'));
        $totalCashOut = array_sum(array_column($cashOutLines, 'amount'));
        $netCashFlow  = $totalCashIn - $totalCashOut;

        $beginningCash = \App\Models\TrialBalance::where('account_name', 'Cash in bank')
            ->latest('created_at')
            ->value('credit_amount') ?? 0;

        return [
            'reports'          => $reports,
            'selectedReportId' => $report->report_id,
            'periodLabel'      => $report->report_period_start->format('F Y'),
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
