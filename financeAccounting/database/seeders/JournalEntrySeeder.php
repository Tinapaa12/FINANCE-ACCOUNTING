<?php // JournalEntrySeeder — creates sample journal entries with lines for testing. Includes example transactions.
namespace Database\Seeders;

use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\GeneralLedger\JournalEntryLine;
use Illuminate\Database\Seeder;

class JournalEntrySeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            [
                'transaction_date' => '2024-06-30',
                'reference_no' => 'JE-2024-00128',
                'description' => 'Collection from Customer A',
                'status' => 'Posted',
                'lines' => [
                    ['account_code' => '1020', 'debit' => 50000, 'credit' => 0],
                    ['account_code' => '1100', 'debit' => 0, 'credit' => 50000],
                ]
            ],
            [
                'transaction_date' => '2024-06-29',
                'reference_no' => 'JE-2024-00127',
                'description' => 'Purchase of Office Supplies on credit',
                'status' => 'Posted',
                'lines' => [
                    ['account_code' => '6100', 'debit' => 25000, 'credit' => 0],
                    ['account_code' => '2100', 'debit' => 0, 'credit' => 25000],
                ]
            ],
            [
                'transaction_date' => '2024-06-28',
                'reference_no' => 'JE-2024-00126',
                'description' => 'Purchase of Office Supply',
                'status' => 'Posted',
                'lines' => [
                    ['account_code' => '6100', 'debit' => 8500, 'credit' => 0],
                    ['account_code' => '1020', 'debit' => 0, 'credit' => 8500],
                ]
            ],
            [
                'transaction_date' => '2024-06-30',
                'reference_no' => 'JE-2024-00129',
                'description' => 'Sales to Customer B (on account)',
                'status' => 'Posted',
                'lines' => [
                    ['account_code' => '1100', 'debit' => 150000, 'credit' => 0],
                    ['account_code' => '4100', 'debit' => 0, 'credit' => 150000],
                ]
            ],
            [
                'transaction_date' => '2024-06-28',
                'reference_no' => 'JE-2024-00130',
                'description' => 'Sales to Customer C (with VAT)',
                'status' => 'Posted',
                'lines' => [
                    ['account_code' => '1100', 'debit' => 112000, 'credit' => 0],
                    ['account_code' => '4100', 'debit' => 0, 'credit' => 100000],
                    ['account_code' => '2300', 'debit' => 0, 'credit' => 12000],
                ]
            ],
        ];

        foreach ($entries as $entryData) {
            $entry = JournalEntry::create([
                'transaction_date' => $entryData['transaction_date'],
                'reference_no' => $entryData['reference_no'],
                'description' => $entryData['description'],
                'status' => $entryData['status'],
            ]);

            foreach ($entryData['lines'] as $line) {
                $account = ChartOfAccount::where('account_code', $line['account_code'])->first();
                if ($account) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->journal_entry_id,
                        'account_id' => $account->account_id,
                        'description' => $entryData['description'],
                        'debit' => $line['debit'],
                        'credit' => $line['credit'],
                    ]);
                }
            }
        }
    }
}