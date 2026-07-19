<?php // DatabaseSeeder — runs ChartOfAccountsSeeder, JournalEntrySeeder, and FinancialReportSeeder in order.
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ChartOfAccountsSeeder::class,
            JournalEntrySeeder::class,
            BudgetVsActualSeeder::class,
        ]);
    }
}
