<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChartOfAccountsController extends Controller
{
    public function index()
    {
        $accounts = [
            ['code' => '1010', 'name' => 'Cash on Hand', 'type' => 'Asset', 'normal_balance' => 'Debit', 'current_balance' => 45200, 'status' => 'Active', 'date_created' => 'June 15, 2025', 'last_updated' => 'June 22, 2025'],
            ['code' => '1020', 'name' => 'Cash in Bank - BDO', 'type' => 'Asset', 'normal_balance' => 'Debit', 'current_balance' => 321400, 'status' => 'Active', 'date_created' => 'June 15, 2025', 'last_updated' => 'June 22, 2025'],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'Asset', 'normal_balance' => 'Debit', 'current_balance' => 248500, 'status' => 'Active', 'date_created' => 'June 15, 2025', 'last_updated' => 'June 22, 2025'],
            ['code' => '1200', 'name' => 'Merchandise Inventory', 'type' => 'Asset', 'normal_balance' => 'Debit', 'current_balance' => 98000, 'status' => 'Active', 'date_created' => 'June 15, 2025', 'last_updated' => 'June 22, 2025'],
            ['code' => '2100', 'name' => 'Accounts Payable', 'type' => 'Liabilities', 'normal_balance' => 'Credit', 'current_balance' => 91200, 'status' => 'Active', 'date_created' => 'June 15, 2025', 'last_updated' => 'June 22, 2025'],
            ['code' => '2300', 'name' => 'Output VAT Payable', 'type' => 'Liabilities', 'normal_balance' => 'Credit', 'current_balance' => 18600, 'status' => 'Active', 'date_created' => 'June 15, 2025', 'last_updated' => 'June 22, 2025'],
            ['code' => '4100', 'name' => 'Sales Revenue', 'type' => 'Revenue', 'normal_balance' => 'Credit', 'current_balance' => 520000, 'status' => 'Active', 'date_created' => 'June 15, 2025', 'last_updated' => 'June 22, 2025'],
            ['code' => '6100', 'name' => 'Salaries and Wages', 'type' => 'Expense', 'normal_balance' => 'Debit', 'current_balance' => 210000, 'status' => 'Active', 'date_created' => 'June 15, 2025', 'last_updated' => 'June 22, 2025'],
            ['code' => '6200', 'name' => 'Rent Expenses', 'type' => 'Expense', 'normal_balance' => 'Debit', 'current_balance' => 45000, 'status' => 'Active', 'date_created' => 'June 15, 2025', 'last_updated' => 'June 22, 2025'],
        ];

        return view('chart-of-accounts.index', compact('accounts'));
    }
}