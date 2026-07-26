<?php
namespace Database\Seeders;

use App\Models\FinancialReporting\BudgetVsActual;
use App\Models\GeneralLedger\ChartOfAccount;
use Illuminate\Database\Seeder;

class BudgetVsActualSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = ChartOfAccount::pluck('account_id', 'account_code');

        BudgetVsActual::create([
            'account_id'          => $accounts['4000'] ?? null,
            'account_name'        => 'Sales Revenue',
            'budget_amount'       => 250000,
            'actual_amount'       => 0,
            'report_period_start' => '2024-06-01',
            'report_period_end'   => '2024-06-30',
        ]);

        BudgetVsActual::create([
            'account_id'          => $accounts['5000'] ?? null,
            'account_name'        => 'Purchases / COGS',
            'budget_amount'       => 100000,
            'actual_amount'       => 0,
            'report_period_start' => '2024-06-01',
            'report_period_end'   => '2024-06-30',
        ]);

        BudgetVsActual::create([
            'account_id'          => $accounts['6100'] ?? null,
            'account_name'        => 'Salaries and Wages',
            'budget_amount'       => 40000,
            'actual_amount'       => 0,
            'report_period_start' => '2024-06-01',
            'report_period_end'   => '2024-06-30',
        ]);

        BudgetVsActual::create([
            'account_id'          => $accounts['6200'] ?? null,
            'account_name'        => 'Rent Expenses',
            'budget_amount'       => 20000,
            'actual_amount'       => 0,
            'report_period_start' => '2024-06-01',
            'report_period_end'   => '2024-06-30',
        ]);

        BudgetVsActual::create([
            'account_id'          => $accounts['4000'] ?? null,
            'account_name'        => 'Sales Revenue',
            'budget_amount'       => 220000,
            'actual_amount'       => 0,
            'report_period_start' => '2026-07-01',
            'report_period_end'   => '2026-07-31',
        ]);

        BudgetVsActual::create([
            'account_id'          => $accounts['5000'] ?? null,
            'account_name'        => 'Purchases / COGS',
            'budget_amount'       => 90000,
            'actual_amount'       => 0,
            'report_period_start' => '2026-07-01',
            'report_period_end'   => '2026-07-31',
        ]);

        BudgetVsActual::create([
            'account_id'          => $accounts['6100'] ?? null,
            'account_name'        => 'Salaries and Wages',
            'budget_amount'       => 35000,
            'actual_amount'       => 0,
            'report_period_start' => '2026-07-01',
            'report_period_end'   => '2026-07-31',
        ]);

        BudgetVsActual::create([
            'account_id'          => $accounts['6200'] ?? null,
            'account_name'        => 'Rent Expenses',
            'budget_amount'       => 18000,
            'actual_amount'       => 0,
            'report_period_start' => '2026-07-01',
            'report_period_end'   => '2026-07-31',
        ]);
    }
}
