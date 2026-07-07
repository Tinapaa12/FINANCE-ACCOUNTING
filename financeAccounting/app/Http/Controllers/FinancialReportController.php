<?php

namespace App\Http\Controllers;

class FinancialReportController extends Controller
{
    public function income()
    {
        return view('reports.income', [
            'month' => 'September',
            'revenue' => [
                ['label' => 'Sales revenue', 'amount' => 520000],
                ['label' => 'Service revenue', 'amount' => 45000],
            ],
            'expenses' => [
                ['label' => 'Cost of good', 'amount' => 280000],
                ['label' => 'Salaries & Wages', 'amount' => 135000],
                ['label' => 'Rent Expense', 'amount' => 15000],
                ['label' => 'Utilities', 'amount' => 8200],
                ['label' => 'Marketing', 'amount' => 18400],
                ['label' => 'Office Supplies', 'amount' => 5000],
            ],
            'trialBalance' => [
                ['account' => 'Cash in bank', 'credit' => 312400, 'debit' => null],
                ['account' => 'Accounts Recievable', 'credit' => 248500, 'debit' => null],
                ['account' => 'Accounts Payable', 'credit' => null, 'debit' => 91200],
                ['account' => 'Sales Revenue', 'credit' => null, 'debit' => 565500],
                ['account' => 'Total Expenses', 'credit' => 461600, 'debit' => null],
            ],
        ]);
    }

    public function assets()
    {
        return view('reports.assets', [
            'assets' => [
                ['label' => 'Cash on hand', 'amount' => 45200],
                ['label' => 'Cash in bank', 'amount' => 312400],
                ['label' => 'Accounts receivable', 'amount' => 248500],
                ['label' => 'Inventory', 'amount' => 98000],
                ['label' => 'Property and equipment', 'amount' => 250000],
            ],
            'liabilities' => [
                ['label' => 'Accounts payable', 'amount' => 91200],
                ['label' => 'VAT payable', 'amount' => 18600],
            ],
            'equity' => [
                ['label' => 'Capital stock', 'amount' => 740900],
                ['label' => 'Retained earnings', 'amount' => 103400],
            ],
        ]);
    }

    public function liabilities()
    {
        return view('reports.liabilities', [
            'reportDate' => 'June 25 2026',
            'budgetVsActual' => [
                ['account' => 'Sales revenue', 'budget' => 500000, 'actual' => 565000, 'status' => 'over'],
                ['account' => 'Salaries & Wages', 'budget' => 130000, 'actual' => 135000, 'status' => 'slightly_over'],
                ['account' => 'Rent Expense', 'budget' => 15000, 'actual' => 15000, 'status' => 'on_budget'],
                ['account' => 'Marketing', 'budget' => 20000, 'actual' => 18400, 'status' => 'under'],
                ['account' => 'Utilities', 'budget' => 7000, 'actual' => 8200, 'status' => 'over'],
            ],
        ]);
    }
}

