<?php // FinancialReportSeeder — generates sample financial reports including Income Statement, Balance Sheet, Cash Flow, and Budget vs Actual.
namespace Database\Seeders;

use App\Models\BalanceSheet;
use App\Models\BalanceSheetLine;
use App\Models\BudgetVsActual;
use App\Models\FinancialReport;
use App\Models\IncomeStatement;
use App\Models\IncomeStatementLine;
use App\Models\TaxRecord;
use App\Models\TrialBalance;
use Illuminate\Database\Seeder;

class FinancialReportSeeder extends Seeder
{
    public function run(): void
    {
        // ===== INCOME STATEMENT (September) =====
        $incomeReport = FinancialReport::create([
            'report_type'         => 'Income Statement',
            'report_period_start' => '2026-09-01',
            'report_period_end'   => '2026-09-30',
            'generated_at'        => now(),
        ]);

        $incomeStatement = IncomeStatement::create([
            'report_id'      => $incomeReport->report_id,
            'total_revenue'  => 0, // auto-calculated below once line items are created
            'total_expenses' => 0, // auto-calculated below once line items are created
        ]);

        $revenueLines = [
            ['line_name' => 'Sales revenue', 'amount' => 520000],
            ['line_name' => 'Service revenue', 'amount' => 45000],
        ];
        foreach ($revenueLines as $i => $line) {
            IncomeStatementLine::create([
                'income_statement_id' => $incomeStatement->income_statement_id,
                'line_name'  => $line['line_name'],
                'category'   => 'revenue',
                'amount'     => $line['amount'],
                'line_order' => $i + 1,
            ]);
        }

        $expenseLines = [
            ['line_name' => 'Cost of good', 'amount' => 280000],
            ['line_name' => 'Salaries & Wages', 'amount' => 135000],
            ['line_name' => 'Rent Expense', 'amount' => 15000],
            ['line_name' => 'Utilities', 'amount' => 8200],
            ['line_name' => 'Marketing', 'amount' => 18400],
            ['line_name' => 'Office Supplies', 'amount' => 5000],
        ];
        foreach ($expenseLines as $i => $line) {
            IncomeStatementLine::create([
                'income_statement_id' => $incomeStatement->income_statement_id,
                'line_name'  => $line['line_name'],
                'category'   => 'expense',
                'amount'     => $line['amount'],
                'line_order' => $i + 1,
            ]);
        }

        // ===== TRIAL BALANCE (tied to the same report) =====
        $trialBalanceRows = [
            ['account_name' => 'Cash in bank',          'debit_amount' => null,   'credit_amount' => 312400],
            ['account_name' => 'Accounts Recievable',   'debit_amount' => null,   'credit_amount' => 248500],
            ['account_name' => 'Accounts Payable',      'debit_amount' => 91200,  'credit_amount' => null],
            ['account_name' => 'Sales Revenue',         'debit_amount' => 565500, 'credit_amount' => null],
            ['account_name' => 'Total Expenses',        'debit_amount' => null,   'credit_amount' => 461600],
        ];
        foreach ($trialBalanceRows as $i => $row) {
            TrialBalance::create([
                'report_id'     => $incomeReport->report_id,
                'account_name'  => $row['account_name'],
                'debit_amount'  => $row['debit_amount'],
                'credit_amount' => $row['credit_amount'],
                'line_order'    => $i + 1,
            ]);
        }

        // ===== BALANCE SHEET =====
        $balanceSheetReport = FinancialReport::create([
            'report_type'         => 'Balance Sheet',
            'report_period_start' => '2026-06-01',
            'report_period_end'   => '2026-06-30',
            'generated_at'        => now(),
        ]);

        $balanceSheet = BalanceSheet::create([
            'report_id'      => $balanceSheetReport->report_id,
            'statement_title'=> 'Balance Sheet',
            'period_label'   => 'As of June 30, 2026',
            'generated_at'   => now(),
        ]);

        $assetLines = [
            ['line_name' => 'Cash on hand', 'amount' => 45200],
            ['line_name' => 'Cash in bank', 'amount' => 312400],
            ['line_name' => 'Accounts receivable', 'amount' => 248500],
            ['line_name' => 'Inventory', 'amount' => 98000],
            ['line_name' => 'Property and equipment', 'amount' => 250000],
        ];
        foreach ($assetLines as $i => $line) {
            BalanceSheetLine::create([
                'balance_sheet_id' => $balanceSheet->balance_sheet_id,
                'line_name'  => $line['line_name'],
                'section'    => 'Asset',
                'amount'     => $line['amount'],
                'line_order' => $i + 1,
            ]);
        }

        $liabilityLines = [
            ['line_name' => 'Accounts payable', 'amount' => 91200],
            ['line_name' => 'VAT payable', 'amount' => 18600],
        ];
        foreach ($liabilityLines as $i => $line) {
            BalanceSheetLine::create([
                'balance_sheet_id' => $balanceSheet->balance_sheet_id,
                'line_name'  => $line['line_name'],
                'section'    => 'Liability',
                'amount'     => $line['amount'],
                'line_order' => $i + 1,
            ]);
        }

        $equityLines = [
            ['line_name' => 'Capital stock', 'amount' => 740900],
            ['line_name' => 'Retained earnings', 'amount' => 103400],
        ];
        foreach ($equityLines as $i => $line) {
            BalanceSheetLine::create([
                'balance_sheet_id' => $balanceSheet->balance_sheet_id,
                'line_name'  => $line['line_name'],
                'section'    => 'Equity',
                'amount'     => $line['amount'],
                'line_order' => $i + 1,
            ]);
        }

        // ===== BUDGET VS ACTUAL =====
        $budgetRows = [
            ['account_name' => 'Sales revenue',     'budget_amount' => 500000, 'actual_amount' => 565000],
            ['account_name' => 'Salaries & Wages',  'budget_amount' => 130000, 'actual_amount' => 135000],
            ['account_name' => 'Rent Expense',      'budget_amount' => 15000,  'actual_amount' => 15000],
            ['account_name' => 'Marketing',         'budget_amount' => 20000,  'actual_amount' => 18400],
            ['account_name' => 'Utilities',         'budget_amount' => 7000,   'actual_amount' => 8200],
        ];
        foreach ($budgetRows as $row) {
            BudgetVsActual::create([
                'account_name'         => $row['account_name'],
                'report_period_start'  => '2026-06-01',
                'report_period_end'    => '2026-06-25',
                'budget_amount'        => $row['budget_amount'],
                'actual_amount'        => $row['actual_amount'],
                'variance_amount'      => 0, // auto-computed by the model's saving() hook
                'status'               => 'On Budget', // auto-computed too
            ]);
        }

        // ===== CASH FLOW STATEMENT =====
        $cashFlowFinancialReport = FinancialReport::create([
            'report_type'         => 'Cash Flow Statement',
            'report_period_start' => '2026-06-01',
            'report_period_end'   => '2026-06-30',
            'generated_at'        => now(),
        ]);

        $cashFlowReport = \App\Models\CashFlowReport::create([
            'report_id'       => $cashFlowFinancialReport->report_id,
            'statement_title' => 'Cash Flow Statement',
            'period_label'    => 'For the Month Ended June 2026',
            'generated_at'    => now(),
        ]);

        $cashFlowLines = [
            ['activity_type' => 'Operating', 'line_name' => 'Cash received from customers', 'amount' => 565000],
            ['activity_type' => 'Operating', 'line_name' => 'Cash paid to suppliers', 'amount' => -280000],
            ['activity_type' => 'Operating', 'line_name' => 'Cash paid for salaries & wages', 'amount' => -135000],
            ['activity_type' => 'Operating', 'line_name' => 'Cash paid for rent and utilities', 'amount' => -23200],

            ['activity_type' => 'Investing', 'line_name' => 'Purchase of equipment', 'amount' => -50000],

            ['activity_type' => 'Financing', 'line_name' => 'Proceeds from capital stock issued', 'amount' => 100000],
            ['activity_type' => 'Financing', 'line_name' => 'Owner withdrawals', 'amount' => -20000],
        ];
        foreach ($cashFlowLines as $i => $line) {
            \App\Models\CashFlowReportLine::create([
                'cash_flow_id'  => $cashFlowReport->cash_flow_id,
                'activity_type' => $line['activity_type'],
                'line_name'     => $line['line_name'],
                'amount'        => $line['amount'],
                'line_order'    => $i + 1,
            ]);
        }

        // ===== TAX RECORDS =====
        $taxRows = [
            ['reference_type' => 'Customer Invoice', 'reference_id' => 1042, 'tax_type' => 'VAT', 'taxable_amount' => 520000, 'tax_rate' => 12, 'filing_status' => 'paid'],
            ['reference_type' => 'Supplier Bill',     'reference_id' => 887,  'tax_type' => 'VAT', 'taxable_amount' => 238333, 'tax_rate' => 12, 'filing_status' => 'paid'],
            ['reference_type' => 'Supplier Bill',     'reference_id' => 891,  'tax_type' => 'EWT', 'taxable_amount' => 85000,  'tax_rate' => 2,  'filing_status' => 'filed'],
            ['reference_type' => 'Customer Invoice', 'reference_id' => 1055, 'tax_type' => 'VAT', 'taxable_amount' => 45000,  'tax_rate' => 12, 'filing_status' => 'pending'],
        ];
        foreach ($taxRows as $row) {
            TaxRecord::create([
                'reference_type' => $row['reference_type'],
                'reference_id'   => $row['reference_id'],
                'tax_type'       => $row['tax_type'],
                'taxable_amount' => $row['taxable_amount'],
                'tax_rate'       => $row['tax_rate'],
                'tax_amount'     => 0, // auto-computed by the model's saving() hook
                'tax_period'     => 'July 2026',
                'filing_status'  => $row['filing_status'],
            ]);
        }
    }
}