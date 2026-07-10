<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class ChartOfAccountsController extends Controller
{
    public function index()
    {
        $accounts = ChartOfAccount::with('parent')->get();
        return view('chart-of-accounts.index', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|max:20|unique:chart_of_accounts',
            'account_name' => 'required|string|max:255',
            'type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'normal_balance' => 'required|in:Debit,Credit',
            'status' => 'required|in:Active,Inactive',
            'parent_account_id' => 'nullable|exists:chart_of_accounts,account_id',
        ]);

        ChartOfAccount::create($validated);
        return redirect()->route('chart-of-accounts.index')->with('success', 'Account created successfully.');
    }

    public function update(Request $request, ChartOfAccount $chartOfAccount)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'normal_balance' => 'required|in:Debit,Credit',
            'status' => 'required|in:Active,Inactive',
            'parent_account_id' => 'nullable|exists:chart_of_accounts,account_id',
        ]);

        $chartOfAccount->update($validated);
        return redirect()->route('chart-of-accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(ChartOfAccount $chartOfAccount)
    {
        $chartOfAccount->delete();
        return redirect()->route('chart-of-accounts.index')->with('success', 'Account deleted successfully.');
    }
}