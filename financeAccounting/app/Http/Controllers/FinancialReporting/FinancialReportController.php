<?php namespace App\Http\Controllers\FinancialReporting;

use App\Http\Controllers\Controller;
use App\Models\FinancialReporting\BudgetVsActual;
use App\Models\FinancialReporting\ComputedFinancialReport;
use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntryLine;
use App\Services\FinancialReportService;
use DB;

class FinancialReportController extends Controller
{
    public function __construct(
        protected FinancialReportService $reportService
    ) {}

    public function income()
    {
        $periods = $this->reportService->getPeriods();
        $selectedPeriod = request('period');
        if (!$selectedPeriod || !in_array($selectedPeriod, $periods)) {
            $selectedPeriod = $periods[0] ?? null;
        }

        [$start, $end] = $this->reportService->parsePeriod($selectedPeriod);

        $stored = $this->reportService->getStoredReport('income_statement', $selectedPeriod);

        if ($stored) {
            $revenue = $stored->where('section', 'Revenue')->values()->map(fn ($r) => [
                'label' => $r->label, 'amount' => (float) $r->amount,
            ])->toArray();
            $expenses = $stored->where('section', 'Expense')->values()->map(fn ($r) => [
                'label' => $r->label, 'amount' => (float) $r->amount,
            ])->toArray();

            $trialBalance = ComputedFinancialReport::where('report_type', 'trial_balance')
                ->where('period_start', $start)->where('period_end', $end)
                ->orderBy('sort_order')->get()
                ->map(fn ($r) => [
                    'account' => $r->label, 'debit' => (float) $r->debit, 'credit' => (float) $r->credit,
                ])->toArray();
        } else {
            $data = $this->reportService->computeAndStoreIncomeData($selectedPeriod);
            $revenue = $data['revenue'];
            $expenses = $data['expenses'];
            $trialBalance = $data['trialBalance'];
        }

        return view('financial-reporting.reports.income', compact(
            'periods', 'selectedPeriod', 'revenue', 'expenses', 'trialBalance'
        ));
    }

    public function incomePdf()
    {
        return view('financial-reporting.pdf.income', $this->incomeData());
    }

    public function assets()
    {
        $periods = $this->reportService->getPeriods();
        $selectedPeriod = request('period');
        if (!$selectedPeriod || !in_array($selectedPeriod, $periods)) {
            $selectedPeriod = $periods[0] ?? null;
        }

        $stored = $this->reportService->getStoredReport('balance_sheet', $selectedPeriod);

        if ($stored) {
            $assets = $stored->where('section', 'Asset')->values()->map(fn ($r) => [
                'label' => $r->label, 'amount' => (float) $r->amount,
            ])->toArray();
            $liabilities = $stored->where('section', 'Liability')->values()->map(fn ($r) => [
                'label' => $r->label, 'amount' => (float) $r->amount,
            ])->toArray();
            $equity = $stored->where('section', 'Equity')->values()->map(fn ($r) => [
                'label' => $r->label, 'amount' => (float) $r->amount,
            ])->toArray();
        } else {
            $data = $this->reportService->computeAndStoreAssetsData($selectedPeriod);
            $assets = $data['assets'];
            $liabilities = $data['liabilities'];
            $equity = $data['equity'];
        }

        $hasData = !empty($assets) || !empty($liabilities) || !empty($equity);

        return view('financial-reporting.reports.assets', compact(
            'periods', 'selectedPeriod', 'assets', 'liabilities', 'equity', 'hasData'
        ));
    }

    public function assetsPdf()
    {
        return view('financial-reporting.pdf.assets', $this->assetsData());
    }

    public function liabilities()
    {
        return redirect()->route('reports.budget');
    }

    public function liabilitiesPdf()
    {
        return redirect()->route('reports.budget.pdf');
    }

    public function budget()
    {
        $periods = $this->reportService->getPeriods();
        $selectedPeriod = request('period');
        if (!$selectedPeriod || !in_array($selectedPeriod, $periods)) {
            $selectedPeriod = $periods[0] ?? null;
        }

        [$start, $end] = $this->reportService->parsePeriod($selectedPeriod);

        $budgetRows = BudgetVsActual::when($start && $end, fn ($q) => $q
                ->whereBetween('report_period_start', [$start, $end])
                ->orWhereBetween('report_period_end', [$start, $end])
            )
            ->orderBy('budget_actual_id')
            ->get();

        if ($budgetRows->isEmpty()) {
            return view('financial-reporting.reports.budget', [
                'periods'        => $periods,
                'selectedPeriod' => $selectedPeriod,
                'budgetVsActual' => [],
            ]);
        }

        $accountNames = $budgetRows->pluck('account_name');
        $coaAccounts = \App\Models\GeneralLedger\ChartOfAccount::whereIn('account_name', $accountNames)
            ->get()->keyBy('account_name');

        $actuals = \App\Models\GeneralLedger\JournalEntryLine::select('chart_of_accounts.account_name',
                'chart_of_accounts.normal_balance',
                \DB::raw('COALESCE(SUM(journal_entry_lines.debit), 0) as debit_total'),
                \DB::raw('COALESCE(SUM(journal_entry_lines.credit), 0) as credit_total'))
            ->join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.account_id')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
            ->where('journal_entries.status', 'Posted')
            ->whereIn('chart_of_accounts.account_name', $accountNames)
            ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
            ->groupBy('chart_of_accounts.account_name', 'chart_of_accounts.normal_balance')
            ->get()->keyBy('account_name');

        $budgetVsActual = $budgetRows->map(function ($row) use ($actuals, $coaAccounts) {
            $budgetAmount = (float) $row->budget_amount;
            $coa = $coaAccounts->get($row->account_name);

            $actualEntry = $actuals->get($row->account_name);
            if ($actualEntry) {
                $debits = (float) $actualEntry->debit_total;
                $credits = (float) $actualEntry->credit_total;
                $actualAmount = $coa && $coa->normal_balance === 'Credit'
                    ? max($credits - $debits, 0)
                    : max($debits - $credits, 0);
            } else {
                $actualAmount = (float) $row->actual_amount;
            }

            $variance = $actualAmount - $budgetAmount;

            return [
                'account' => $row->account_name,
                'budget'  => $budgetAmount,
                'actual'  => $actualAmount,
                'status'  => match (true) {
                    $variance > 0 && $variance / max($budgetAmount, 1) < 0.05 => 'slightly_over',
                    $variance > 0 => 'over',
                    $variance < 0 => 'under',
                    default       => 'on_budget',
                },
            ];
        })->toArray();

        return view('financial-reporting.reports.budget', compact('periods', 'selectedPeriod', 'budgetVsActual'));
    }

    public function budgetPdf()
    {
        return view('financial-reporting.pdf.budget', $this->budgetData());
    }

    public function cashflow()
    {
        $periods = $this->reportService->getPeriods();
        $selectedPeriod = request('period');
        if (!$selectedPeriod || !in_array($selectedPeriod, $periods)) {
            $selectedPeriod = $periods[0] ?? null;
        }

        [$start, $end] = $this->reportService->parsePeriod($selectedPeriod);

        $stored = $this->reportService->getStoredReport('cash_flow', $selectedPeriod);

        if ($stored) {
            $cashInLines = $stored->where('section', 'cash_in')->values()->map(fn ($r) => [
                'label' => $r->label, 'amount' => (float) $r->amount,
            ])->toArray();
            $cashOutLines = $stored->where('section', 'cash_out')->values()->map(fn ($r) => [
                'label' => $r->label, 'amount' => (float) $r->amount,
            ])->toArray();

            $totalCashIn  = collect($cashInLines)->sum('amount');
            $totalCashOut = collect($cashOutLines)->sum('amount');
            $netCashFlow  = $totalCashIn - $totalCashOut;
            $beginningCash = 0;
            $endingCash = $beginningCash + $netCashFlow;
        } else {
            $data = $this->reportService->computeAndStoreCashflowData($selectedPeriod);
            $cashInLines = $data['cashInLines']->toArray();
            $cashOutLines = $data['cashOutLines']->toArray();
            $totalCashIn = $data['totalCashIn'];
            $totalCashOut = $data['totalCashOut'];
            $netCashFlow = $data['netCashFlow'];
            $beginningCash = $data['beginningCash'];
            $endingCash = $data['endingCash'];
        }

        $periodLabel = $selectedPeriod ?? 'All';
        $hasData = true;

        return view('financial-reporting.reports.cashflow', compact(
            'periods', 'selectedPeriod', 'periodLabel',
            'cashInLines', 'cashOutLines',
            'totalCashIn', 'totalCashOut', 'netCashFlow',
            'beginningCash', 'endingCash', 'hasData'
        ));
    }

    public function cashflowPdf()
    {
        return view('financial-reporting.pdf.cashflow', $this->cashflowData());
    }

    public function regenerate()
    {
        $period = request('period');
        $reportType = request('report_type');

        $types = $reportType ? [$reportType] : ['income_statement', 'trial_balance', 'balance_sheet', 'cash_flow'];

        foreach ($types as $type) {
            match ($type) {
                'income_statement', 'trial_balance' => $this->reportService->computeAndStoreIncomeData($period),
                'balance_sheet' => $this->reportService->computeAndStoreAssetsData($period),
                'cash_flow' => $this->reportService->computeAndStoreCashflowData($period),
                default => null,
            };
        }

        $referer = request()->header('Referer') ?? route('reports.income');
        return redirect($referer)->with('success', 'Report data regenerated successfully.');
    }

    private function incomeData(): array
    {
        $periods = $this->reportService->getPeriods();
        $selectedPeriod = request('period');
        if (!$selectedPeriod || !in_array($selectedPeriod, $periods)) {
            $selectedPeriod = $periods[0] ?? null;
        }

        [$start, $end] = $this->reportService->parsePeriod($selectedPeriod);

        $stored = $this->reportService->getStoredReport('income_statement', $selectedPeriod);

        if ($stored) {
            $revenue = $stored->where('section', 'Revenue')->values()->map(fn ($r) => [
                'label' => $r->label, 'amount' => (float) $r->amount,
            ])->toArray();
            $expenses = $stored->where('section', 'Expense')->values()->map(fn ($r) => [
                'label' => $r->label, 'amount' => (float) $r->amount,
            ])->toArray();
            $trialBalance = ComputedFinancialReport::where('report_type', 'trial_balance')
                ->where('period_start', $start)->where('period_end', $end)
                ->orderBy('sort_order')->get()
                ->map(fn ($r) => [
                    'account' => $r->label, 'debit' => (float) $r->debit, 'credit' => (float) $r->credit,
                ])->toArray();
        } else {
            $data = $this->reportService->computeAndStoreIncomeData($selectedPeriod);
            $revenue = $data['revenue'];
            $expenses = $data['expenses'];
            $trialBalance = $data['trialBalance'];
        }

        return compact('periods', 'selectedPeriod', 'revenue', 'expenses', 'trialBalance');
    }

    private function assetsData(): array
    {
        $periods = $this->reportService->getPeriods();
        $selectedPeriod = request('period');
        if (!$selectedPeriod || !in_array($selectedPeriod, $periods)) {
            $selectedPeriod = $periods[0] ?? null;
        }

        $stored = $this->reportService->getStoredReport('balance_sheet', $selectedPeriod);

        if ($stored) {
            $assets = $stored->where('section', 'Asset')->values()->map(fn ($r) => [
                'label' => $r->label, 'amount' => (float) $r->amount,
            ])->toArray();
            $liabilities = $stored->where('section', 'Liability')->values()->map(fn ($r) => [
                'label' => $r->label, 'amount' => (float) $r->amount,
            ])->toArray();
            $equity = $stored->where('section', 'Equity')->values()->map(fn ($r) => [
                'label' => $r->label, 'amount' => (float) $r->amount,
            ])->toArray();
        } else {
            $data = $this->reportService->computeAndStoreAssetsData($selectedPeriod);
            $assets = $data['assets'];
            $liabilities = $data['liabilities'];
            $equity = $data['equity'];
        }

        return compact('periods', 'selectedPeriod', 'assets', 'liabilities', 'equity');
    }

    private function budgetData(): array
    {
        $periods = $this->reportService->getPeriods();
        $selectedPeriod = request('period');
        if (!$selectedPeriod || !in_array($selectedPeriod, $periods)) {
            $selectedPeriod = $periods[0] ?? null;
        }

        [$start, $end] = $this->reportService->parsePeriod($selectedPeriod);

        $budgetRows = \App\Models\FinancialReporting\BudgetVsActual::when($start && $end, fn ($q) => $q
                ->whereBetween('report_period_start', [$start, $end])
                ->orWhereBetween('report_period_end', [$start, $end])
            )
            ->orderBy('budget_actual_id')
            ->get();

        if ($budgetRows->isEmpty()) {
            return compact('periods', 'selectedPeriod') + ['budgetVsActual' => []];
        }

        $accountNames = $budgetRows->pluck('account_name');
        $coaAccounts = \App\Models\GeneralLedger\ChartOfAccount::whereIn('account_name', $accountNames)
            ->get()->keyBy('account_name');

        $actuals = \App\Models\GeneralLedger\JournalEntryLine::select('chart_of_accounts.account_name',
                'chart_of_accounts.normal_balance',
                \DB::raw('COALESCE(SUM(journal_entry_lines.debit), 0) as debit_total'),
                \DB::raw('COALESCE(SUM(journal_entry_lines.credit), 0) as credit_total'))
            ->join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.account_id')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
            ->where('journal_entries.status', 'Posted')
            ->whereIn('chart_of_accounts.account_name', $accountNames)
            ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
            ->groupBy('chart_of_accounts.account_name', 'chart_of_accounts.normal_balance')
            ->get()->keyBy('account_name');

        $budgetVsActual = $budgetRows->map(function ($row) use ($actuals, $coaAccounts) {
            $budgetAmount = (float) $row->budget_amount;
            $coa = $coaAccounts->get($row->account_name);
            $actualEntry = $actuals->get($row->account_name);

            if ($actualEntry) {
                $actualAmount = $coa && $coa->normal_balance === 'Credit'
                    ? max((float) $actualEntry->credit_total - (float) $actualEntry->debit_total, 0)
                    : max((float) $actualEntry->debit_total - (float) $actualEntry->credit_total, 0);
            } else {
                $actualAmount = (float) $row->actual_amount;
            }

            $variance = $actualAmount - $budgetAmount;

            return [
                'account' => $row->account_name,
                'budget'  => $budgetAmount,
                'actual'  => $actualAmount,
                'status'  => match (true) {
                    $variance > 0 && $variance / max($budgetAmount, 1) < 0.05 => 'slightly_over',
                    $variance > 0 => 'over',
                    $variance < 0 => 'under',
                    default       => 'on_budget',
                },
            ];
        })->toArray();

        return compact('periods', 'selectedPeriod', 'budgetVsActual');
    }

    private function cashflowData(): array
    {
        $periods = $this->reportService->getPeriods();
        $selectedPeriod = request('period');
        if (!$selectedPeriod || !in_array($selectedPeriod, $periods)) {
            $selectedPeriod = $periods[0] ?? null;
        }

        $stored = $this->reportService->getStoredReport('cash_flow', $selectedPeriod);

        if ($stored) {
            $cashInLines = $stored->where('section', 'cash_in')->values()->map(fn ($r) => [
                'label' => $r->label, 'amount' => (float) $r->amount,
            ])->toArray();
            $cashOutLines = $stored->where('section', 'cash_out')->values()->map(fn ($r) => [
                'label' => $r->label, 'amount' => (float) $r->amount,
            ])->toArray();

            $totalCashIn  = collect($cashInLines)->sum('amount');
            $totalCashOut = collect($cashOutLines)->sum('amount');
            $netCashFlow  = $totalCashIn - $totalCashOut;
        } else {
            $data = $this->reportService->computeAndStoreCashflowData($selectedPeriod);
            $cashInLines = $data['cashInLines']->toArray();
            $cashOutLines = $data['cashOutLines']->toArray();
            $totalCashIn = $data['totalCashIn'];
            $totalCashOut = $data['totalCashOut'];
            $netCashFlow = $data['netCashFlow'];
        }

        return compact('periods', 'selectedPeriod', 'cashInLines', 'cashOutLines', 'totalCashIn', 'totalCashOut', 'netCashFlow');
    }
}
