<?php
namespace Database\Seeders;

use App\Models\FinancialReporting\FinancialReport;
use Illuminate\Database\Seeder;

class FinancialReportSeeder extends Seeder
{
    public function run(): void
    {
        // Seed one period matching the seeded journal entries (June 2024)
        FinancialReport::create([
            'report_type'         => 'Income Statement',
            'report_period_start' => '2024-06-01',
            'report_period_end'   => '2024-06-30',
            'generated_at'        => now(),
        ]);

        FinancialReport::create([
            'report_type'         => 'Balance Sheet',
            'report_period_start' => '2024-06-01',
            'report_period_end'   => '2024-06-30',
            'generated_at'        => now(),
        ]);

        FinancialReport::create([
            'report_type'         => 'Cash Flow Statement',
            'report_period_start' => '2024-06-01',
            'report_period_end'   => '2024-06-30',
            'generated_at'        => now(),
        ]);
    }
}
