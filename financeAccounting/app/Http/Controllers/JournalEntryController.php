<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function index()
    {
        $entries = [
            [
                'id' => 1,
                'date' => 'June 30, 2024',
                'reference' => 'JE - 2024 - 00128',
                'description' => 'Collection from Customer A',
                'status' => 'Posted',
                'debit' => 50000.00,
                'credit' => 50000.00,
                'created_at' => 'June 30, 2024',
                'lines' => [
                    ['account_code' => '1100 - 000', 'account_name' => 'Cash in Bank', 'description' => 'Collection from...', 'debit' => 50000.00, 'credit' => 0.00],
                    ['account_code' => '1200 - 000', 'account_name' => 'Accounts Receivable', 'description' => 'Collection from...', 'debit' => 0.00, 'credit' => 50000.00],
                ]
            ],
            [
                'id' => 2,
                'date' => 'June 29, 2024',
                'reference' => 'JE - 2024 - 00127',
                'description' => 'Payment to Supplier X',
                'status' => 'Posted',
                'debit' => 25000.00,
                'credit' => 25000.00,
                'created_at' => 'June 29, 2024',
                'lines' => [
                    ['account_code' => '2100 - 000', 'account_name' => 'Accounts Payable', 'description' => 'Payment to...', 'debit' => 25000.00, 'credit' => 0.00],
                    ['account_code' => '1020 - 000', 'account_name' => 'Cash in Bank', 'description' => 'Payment to...', 'debit' => 0.00, 'credit' => 25000.00],
                ]
            ],
            [
                'id' => 3,
                'date' => 'June 28, 2024',
                'reference' => 'JE - 2024 - 00126',
                'description' => 'Purchase of Office Supply',
                'status' => 'Posted',
                'debit' => 8500.00,
                'credit' => 8500.00,
                'created_at' => 'June 28, 2024',
                'lines' => [
                    ['account_code' => '6100 - 000', 'account_name' => 'Office Supplies', 'description' => 'Purchase of...', 'debit' => 8500.00, 'credit' => 0.00],
                    ['account_code' => '1020 - 000', 'account_name' => 'Cash in Bank', 'description' => 'Purchase of...', 'debit' => 0.00, 'credit' => 8500.00],
                ]
            ],
            [
                'id' => 4,
                'date' => 'June 27, 2024',
                'reference' => 'JE - 2024 - 00125',
                'description' => 'Monthly Salary Expense',
                'status' => 'Posted',
                'debit' => 15000.00,
                'credit' => 15000.00,
                'created_at' => 'June 27, 2024',
                'lines' => [
                    ['account_code' => '6100 - 000', 'account_name' => 'Salaries and Wages', 'description' => 'Monthly salary...', 'debit' => 15000.00, 'credit' => 0.00],
                    ['account_code' => '1020 - 000', 'account_name' => 'Cash in Bank', 'description' => 'Monthly salary...', 'debit' => 0.00, 'credit' => 15000.00],
                ]
            ],
            [
                'id' => 5,
                'date' => 'June 26, 2024',
                'reference' => 'JE - 2024 - 00124',
                'description' => 'Utility Bill Payment',
                'status' => 'Posted',
                'debit' => 1800.00,
                'credit' => 1800.00,
                'created_at' => 'June 26, 2024',
                'lines' => [
                    ['account_code' => '6200 - 000', 'account_name' => 'Utilities Expense', 'description' => 'Utility bill...', 'debit' => 1800.00, 'credit' => 0.00],
                    ['account_code' => '1020 - 000', 'account_name' => 'Cash in Bank', 'description' => 'Utility bill...', 'debit' => 0.00, 'credit' => 1800.00],
                ]
            ],
        ];

        return view('journal-entries.index', compact('entries'));
    }
}