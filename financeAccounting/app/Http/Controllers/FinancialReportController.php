<?php // FinancialReportController — serves financial report pages and their PDF versions. Provides Income Statement, Balance Sheet, Cash Flow, and Budget vs Actual views with data from generated report models.
namespace App\Http\Controllers;

use App\Models\BalanceSheet;
use App\Models\BudgetVsActual;
use App\Models\CashFlowReport;
use App\Models\FinancialReport;
use App\Models\IncomeStatement;

class FinancialReportController extends Controller
{
    public function income()
    {
        return view('reports.income', $this->incomeData());
    }

    public function incomePdf()
    {
        return view('pdf.income', $this->incomeData());
    }

    public function assets()
    {
        return view('reports.assets', $this->assetsData());
    }

    public function assetsPdf()
    {
        return view('pdf.assets', $this->assetsData());
    }

    public function liabilities()
    {
        return view('reports.liabilities', $this->liabilitiesData());
    }

    public function liabilitiesPdf()
    {
        return view('pdf.liabilities', $this->liabilitiesData());
    }

    public function cashflow()
    {
        return view('reports.cashflow', $this->cashflowData());
    }

    public function cashflowPdf()
    {
        return view('pdf.cashflow', $this->cashflowData());
    }

    private function incomeData(): array
    {
        // Pull the most recently generated Income Statement report
        $report = FinancialReport::where('report_type', 'Income Statement')
            ->latest('generated_at')
            ->firstOrFail();

        $incomeStatement = IncomeStatement::where('report_id', $report->report_id)->firstOrFail();

        $revenue = $incomeStatement->revenueLines()->get()
            ->map(fn ($line) => ['label' => $line->line_name, 'amount' => (float) $line->amount])
            ->toArray();

        $expenses = $incomeStatement->expenseLines()->get()
            ->map(fn ($line) => ['label' => $line->line_name, 'amount' => (float) $line->amount])
            ->toArray();

        $trialBalance = $report->trialBalanceLines()->orderBy('line_order')->get()
            ->map(fn ($row) => [
                'account' => $row->account_name,
                'debit'   => $row->debit_amount,
                'credit'  => $row->credit_amount,
            ])->toArray();

        return [
            'month'        => $report->report_period_start->format('F'),
            'revenue'      => $revenue,
            'expenses'     => $expenses,
            'trialBalance' => $trialBalance,
        ];
    }

    private function assetsData(): array
    {
        $balanceSheet = BalanceSheet::with('report')->latest('generated_at')->firstOrFail();

        $mapLine = fn ($line) => ['label' => $line->line_name, 'amount' => (float) $line->amount];

        return [
            'assets'      => $balanceSheet->assets()->get()->map($mapLine)->toArray(),
            'liabilities' => $balanceSheet->liabilities()->get()->map($mapLine)->toArray(),
            'equity'      => $balanceSheet->equity()->get()->map($mapLine)->toArray(),
        ];
    }

    private function liabilitiesData(): array
    {
        $rows = BudgetVsActual::orderBy('created_at')->get();

        return [
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
        $cashFlow = CashFlowReport::with('report')->latest('generated_at')->firstOrFail();

        $mapLine = fn ($line) => ['label' => $line->line_name, 'amount' => (float) $line->amount];

        // Beginning cash = whatever cash-in-bank balance was on the latest Trial Balance,
        // so the statement ties back to the books instead of using a made-up number.
        $beginningCash = \App\Models\TrialBalance::where('account_name', 'Cash in bank')
            ->latest('created_at')
            ->value('credit_amount') ?? 0;

        return [
            'periodLabel'    => $cashFlow->period_label,
            'operating'      => $cashFlow->operatingLines()->get()->map($mapLine)->toArray(),
            'investing'      => $cashFlow->investingLines()->get()->map($mapLine)->toArray(),
            'financing'      => $cashFlow->financingLines()->get()->map($mapLine)->toArray(),
            'totalOperating' => $cashFlow->total_operating,
            'totalInvesting' => $cashFlow->total_investing,
            'totalFinancing' => $cashFlow->total_financing,
            'netCashFlow'    => $cashFlow->net_cash_flow,
            'beginningCash'  => (float) $beginningCash,
            'endingCash'     => (float) $beginningCash + $cashFlow->net_cash_flow,
        ];
    }
}
