<?php
namespace App\Http\Controllers\FinancialReporting;

use App\Http\Controllers\Controller;
use App\Models\BalanceSheet;
use App\Models\BalanceSheetLine;
use App\Models\BudgetVsActual;
use App\Models\CashFlowReport;
use App\Models\CashFlowReportLine;
use App\Models\FinancialReport;
use App\Models\IncomeStatement;
use App\Models\IncomeStatementLine;
use App\Models\TaxRecord;
use App\Models\TrialBalance;
use Illuminate\Http\Request;

class ManageDataController extends Controller
{
    public function index()
    {
        return view('financial-reporting.manage.index', [
            'reports'        => FinancialReport::orderByDesc('report_period_start')->get(),
            'periods'        => TaxRecord::select('tax_period')->distinct()->orderByDesc('tax_period')->pluck('tax_period'),
            'reportPeriods'  => FinancialReport::select('report_period_start')->distinct()->orderByDesc('report_period_start')->get()->map(fn($r) => \Carbon\Carbon::parse($r->report_period_start)->format('F Y'))->unique(),
            'balanceSheet' => BalanceSheet::latest('generated_at')->first(),
            'cashFlow'     => CashFlowReport::latest('generated_at')->first(),
        ]);
    }

    // === Income Statement ===

    public function storeReport(Request $request)
    {
        $data = $request->validate([
            'report_period_start' => 'required|date',
            'report_period_end'   => 'required|date',
        ]);

        $report = FinancialReport::create([
            'report_type'         => 'Income Statement',
            'report_period_start' => $data['report_period_start'],
            'report_period_end'   => $data['report_period_end'],
            'generated_at'        => now(),
        ]);

        IncomeStatement::create([
            'report_id'      => $report->report_id,
            'total_revenue'  => 0,
            'total_expenses' => 0,
        ]);

        return redirect()->route('reports.manage')->with('success', 'Report period created. Now add lines below.');
    }

    public function storeIncomeLine(Request $request)
    {
        $data = $request->validate([
            'income_statement_id' => 'required|exists:income_statements,income_statement_id',
            'line_name'  => 'required|string|max:255',
            'category'   => 'required|in:revenue,expense',
            'amount'     => 'required|numeric|min:0',
        ]);

        $stm = IncomeStatement::findOrFail($data['income_statement_id']);
        $report = $stm->report;

        IncomeStatementLine::create([
            'income_statement_id' => $data['income_statement_id'],
            'line_name'  => $data['line_name'],
            'category'   => $data['category'],
            'amount'     => $data['amount'],
            'line_order' => 0,
            'report_period_start' => $report->report_period_start,
            'report_period_end'   => $report->report_period_end,
        ]);

        return redirect()->route('reports.manage', ['tab' => 'income'])->with('success', 'Line added.');
    }

    public function destroyIncomeLine(IncomeStatementLine $line)
    {
        $line->delete();
        return redirect()->route('reports.manage', ['tab' => 'income'])->with('success', 'Line deleted.');
    }

    // === Trial Balance ===

    public function storeTrialBalance(Request $request)
    {
        $data = $request->validate([
            'report_id'     => 'required|exists:financial_reports,report_id',
            'account_name'  => 'required|string|max:255',
            'debit_amount'  => 'nullable|numeric|min:0',
            'credit_amount' => 'nullable|numeric|min:0',
        ]);

        TrialBalance::create([
            'report_id'     => $data['report_id'],
            'account_name'  => $data['account_name'],
            'debit_amount'  => $data['debit_amount'],
            'credit_amount' => $data['credit_amount'],
            'line_order'    => 0,
        ]);

        return redirect()->route('reports.manage', ['tab' => 'trial'])->with('success', 'Entry added.');
    }

    public function destroyTrialBalance(TrialBalance $trialBalance)
    {
        $trialBalance->delete();
        return redirect()->route('reports.manage', ['tab' => 'trial'])->with('success', 'Entry deleted.');
    }

    // === Tax Records ===

    public function storeTaxRecord(Request $request)
    {
        $data = $request->validate([
            'reference_type' => 'required|string|max:255',
            'reference_id'   => 'required|integer',
            'tax_type'       => 'required|string|max:255',
            'taxable_amount' => 'required|numeric|min:0',
            'tax_rate'       => 'required|numeric|min:0',
            'tax_period'     => 'required|string|max:255',
            'filing_status'  => 'required|in:paid,filed,pending',
        ]);

        TaxRecord::create($data);

        return redirect()->route('reports.manage', ['tab' => 'tax'])->with('success', 'Tax record added.');
    }

    public function destroyTaxRecord(TaxRecord $taxRecord)
    {
        $taxRecord->delete();
        return redirect()->route('reports.manage', ['tab' => 'tax'])->with('success', 'Tax record deleted.');
    }

    // === Budget vs Actual ===

    public function storeBudget(Request $request)
    {
        $data = $request->validate([
            'account_name'  => 'required|string|max:255',
            'budget_amount' => 'required|numeric|min:0',
            'tax_period'    => 'required|string|max:255',
        ]);

        $start = \Carbon\Carbon::createFromFormat('F Y', $data['tax_period'])->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        BudgetVsActual::create([
            'account_name'        => $data['account_name'],
            'budget_amount'       => $data['budget_amount'],
            'actual_amount'       => 0,
            'report_period_start' => $start,
            'report_period_end'   => $end,
        ]);

        return redirect()->route('reports.manage', ['tab' => 'budget'])->with('success', 'Budget entry added.');
    }

    public function destroyBudget(BudgetVsActual $budgetVsActual)
    {
        $budgetVsActual->delete();
        return redirect()->route('reports.manage', ['tab' => 'budget'])->with('success', 'Budget entry deleted.');
    }

    // === Balance Sheet ===

    public function storeBalanceSheet(Request $request)
    {
        $data = $request->validate([
            'report_id' => 'nullable|exists:financial_reports,report_id',
            'line_name' => 'required|string|max:255',
            'section'   => 'required|in:Asset,Liability,Equity',
            'amount'    => 'required|numeric|min:0',
        ]);

        $existing = null;
        if ($data['report_id']) {
            $existing = FinancialReport::find($data['report_id']);
        }

        if ($existing && $existing->report_type === 'Balance Sheet') {
            $report = $existing;
        } else {
            $periodStart = $existing ? $existing->report_period_start : now()->startOfMonth();
            $periodEnd   = $existing ? $existing->report_period_end   : now()->endOfMonth();
            $report = FinancialReport::create([
                'report_type'         => 'Balance Sheet',
                'report_period_start' => $periodStart,
                'report_period_end'   => $periodEnd,
                'generated_at'        => now(),
            ]);
        }

        $bs = BalanceSheet::firstOrCreate(
            ['report_id' => $report->report_id],
            [
                'statement_title' => 'Balance Sheet',
                'period_label'    => 'As of ' . $report->report_period_end->format('F j, Y'),
                'generated_at'    => now(),
            ]
        );

        BalanceSheetLine::create([
            'balance_sheet_id' => $bs->balance_sheet_id,
            'line_name'  => $data['line_name'],
            'section'    => $data['section'],
            'amount'     => $data['amount'],
            'line_order' => 0,
        ]);

        return redirect()->route('reports.manage', ['tab' => 'balance'])->with('success', 'Balance sheet line added.');
    }

    public function destroyBalanceLine(BalanceSheetLine $line)
    {
        $line->delete();
        return redirect()->route('reports.manage', ['tab' => 'balance'])->with('success', 'Line deleted.');
    }

    // === Cash Flow ===

    public function storeCashFlow(Request $request)
    {
        $data = $request->validate([
            'report_id'    => 'nullable|exists:financial_reports,report_id',
            'flow_type'    => 'required|in:Cash In,Cash Out',
            'account_name' => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
        ]);

        $report = null;
        if ($data['report_id']) {
            $report = FinancialReport::find($data['report_id']);
        }

        if (!$report) {
            $report = FinancialReport::create([
                'report_type'         => 'Cash Flow Statement',
                'report_period_start' => now()->startOfMonth(),
                'report_period_end'   => now()->endOfMonth(),
                'generated_at'        => now(),
            ]);
        }

        $cf = CashFlowReport::firstOrCreate(
            ['report_id' => $report->report_id],
            [
                'statement_title' => 'Cash Flow Statement',
                'period_label'    => 'For the Month Ended ' . now()->format('F Y'),
                'generated_at'    => now(),
            ]
        );

        CashFlowReportLine::create([
            'cash_flow_id'  => $cf->cash_flow_id,
            'activity_type' => $data['flow_type'],
            'line_name'     => $data['account_name'],
            'amount'        => $data['flow_type'] === 'Cash Out' ? -abs($data['amount']) : abs($data['amount']),
            'line_order'    => 0,
        ]);

        return redirect()->route('reports.manage', ['tab' => 'cashflow'])->with('success', 'Cash flow entry added.');
    }

    public function destroyCashFlowLine(CashFlowReportLine $line)
    {
        $line->delete();
        return redirect()->route('reports.manage', ['tab' => 'cashflow'])->with('success', 'Line deleted.');
    }
}
