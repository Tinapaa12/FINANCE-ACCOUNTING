<?php // ChartOfAccountsSeeder — populates chart_of_accounts with standard account codes for all account types.
namespace Database\Seeders;

use App\Models\GeneralLedger\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['account_code' => '1010', 'account_name' => 'Cash on Hand',           'normal_balance' => 'Debit',  'type' => 'Asset',     'status' => 'Active'],
            ['account_code' => '1020', 'account_name' => 'Cash in Bank - BDO',      'normal_balance' => 'Debit',  'type' => 'Asset',     'status' => 'Active'],
            ['account_code' => '1100', 'account_name' => 'Accounts Receivable',     'normal_balance' => 'Debit',  'type' => 'Asset',     'status' => 'Active'],
            ['account_code' => '1200', 'account_name' => 'Merchandise Inventory',   'normal_balance' => 'Debit',  'type' => 'Asset',     'status' => 'Active'],
            ['account_code' => '2100', 'account_name' => 'Accounts Payable',        'normal_balance' => 'Credit', 'type' => 'Liability',  'status' => 'Active'],
            ['account_code' => '2300', 'account_name' => 'Output VAT Payable',      'normal_balance' => 'Credit', 'type' => 'Liability',  'status' => 'Active'],
            ['account_code' => '4100', 'account_name' => 'Sales Revenue',           'normal_balance' => 'Credit', 'type' => 'Revenue',   'status' => 'Active'],
            ['account_code' => '5000', 'account_name' => 'Purchases / COGS',        'normal_balance' => 'Debit',  'type' => 'Expense',   'status' => 'Active'],
            ['account_code' => '6100', 'account_name' => 'Salaries and Wages',      'normal_balance' => 'Debit',  'type' => 'Expense',   'status' => 'Active'],
            ['account_code' => '6200', 'account_name' => 'Rent Expenses',           'normal_balance' => 'Debit',  'type' => 'Expense',   'status' => 'Active'],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::firstOrCreate(
                ['account_code' => $account['account_code']],
                $account
            );
        }
    }
}