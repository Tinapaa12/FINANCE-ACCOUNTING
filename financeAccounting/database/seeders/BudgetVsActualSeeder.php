<?php // BudgetVsActualSeeder — seeds budget targets for accounts that have journal entry activity.
namespace Database\Seeders;

use App\Models\FinancialReporting\BudgetVsActual;
use Illuminate\Database\Seeder;

class BudgetVsActualSeeder extends Seeder
{
    public function run(): void
    {
        BudgetVsActual::create([
            'account_name'        => 'Sales Revenue',
            'budget_amount'       => 120000,
            'actual_amount'       => 0,
            'report_period_start' => '2024-06-01',
            'report_period_end'   => '2024-06-30',
        ]);

        BudgetVsActual::create([
            'account_name'        => 'Salaries and Wages',
            'budget_amount'       => 10000,
            'actual_amount'       => 0,
            'report_period_start' => '2024-06-01',
            'report_period_end'   => '2024-06-30',
        ]);
    }
}
