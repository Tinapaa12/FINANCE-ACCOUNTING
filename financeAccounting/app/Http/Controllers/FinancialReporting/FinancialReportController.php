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

        $balanceSheet = BalanceSheet::where('report_id', $report->report_id)->first();

        if (!$balanceSheet) {
            return ['assets' => [], 'liabilities' => [], 'equity' => [], 'reports' => $reports, 'selectedReportId' => $report->report_id, 'hasData' => false];
        }

        $mapLine = fn ($line) => ['label' => $line->line_name, 'amount' => (float) $line->amount];

        return [
            'assets'           => $balanceSheet->assets()->get()->map($mapLine)->toArray(),
            'liabilities'      => $balanceSheet->liabilities()->get()->map($mapLine)->toArray(),
            'equity'           => $balanceSheet->equity()->get()->map($mapLine)->toArray(),
            'reports'          => $reports,
            'selectedReportId' => $report->report_id,
            'hasData'          => true,
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
                'periodLabel' => '', 'operating' => [], 'investing' => [], 'financing' => [],
                'totalOperating' => 0, 'totalInvesting' => 0, 'totalFinancing' => 0,
                'netCashFlow' => 0, 'beginningCash' => 0, 'endingCash' => 0, 'hasData' => false,
            ];
        }

        $cashFlow = CashFlowReport::where('report_id', $report->report_id)->first();

        if (!$cashFlow) {
            return [
                'reports' => $reports, 'selectedReportId' => $report->report_id,
                'periodLabel' => '', 'operating' => [], 'investing' => [], 'financing' => [],
                'totalOperating' => 0, 'totalInvesting' => 0, 'totalFinancing' => 0,
                'netCashFlow' => 0, 'beginningCash' => 0, 'endingCash' => 0, 'hasData' => false,
            ];
        }

        $mapLine = fn ($line) => ['label' => $line->line_name, 'amount' => (float) $line->amount];

        $beginningCash = \App\Models\TrialBalance::where('account_name', 'Cash in bank')
            ->latest('created_at')
            ->value('credit_amount') ?? 0;

        return [
            'reports'          => $reports,
            'selectedReportId' => $report->report_id,
            'periodLabel'      => $cashFlow->period_label,
            'operating'        => $cashFlow->operatingLines()->get()->map($mapLine)->toArray(),
            'investing'        => $cashFlow->investingLines()->get()->map($mapLine)->toArray(),
            'financing'        => $cashFlow->financingLines()->get()->map($mapLine)->toArray(),
            'totalOperating'   => $cashFlow->total_operating,
            'totalInvesting'   => $cashFlow->total_investing,
            'totalFinancing'   => $cashFlow->total_financing,
            'netCashFlow'      => $cashFlow->net_cash_flow,
            'beginningCash'    => (float) $beginningCash,
            'endingCash'       => (float) $beginningCash + $cashFlow->net_cash_flow,
            'hasData'          => true,
        ];
    }
}
