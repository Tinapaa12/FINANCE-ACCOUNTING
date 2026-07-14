<?php // ChartOfAccountsController — manages the Chart of Accounts CRUD. Lists accounts with computed balances from journal entry lines, and handles create/update/delete with JSON support for AJAX.
namespace App\Http\Controllers\GeneralLedger;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsController extends Controller
{
    public function pdf()
    {
        $accounts = ChartOfAccount::with('parent')
            ->select('chart_of_accounts.*')
            ->addSelect(DB::raw('COALESCE((
                SELECT CASE WHEN chart_of_accounts.normal_balance = "Debit"
                    THEN SUM(jel.debit) - SUM(jel.credit)
                    ELSE SUM(jel.credit) - SUM(jel.debit)
                END
                FROM journal_entry_lines jel
                WHERE jel.account_id = chart_of_accounts.account_id
            ), 0) as current_balance'))
            ->get();

        return view('general-ledger.pdf.chart-of-accounts', compact('accounts'));
    }

    public function index()
    {
        $accounts = ChartOfAccount::with('parent')
            ->select('chart_of_accounts.*')
            ->addSelect(DB::raw('COALESCE((
                SELECT CASE WHEN chart_of_accounts.normal_balance = "Debit"
                    THEN SUM(jel.debit) - SUM(jel.credit)
                    ELSE SUM(jel.credit) - SUM(jel.debit)
                END
                FROM journal_entry_lines jel
                WHERE jel.account_id = chart_of_accounts.account_id
            ), 0) as current_balance'))
            ->paginate(10);

        return view('general-ledger.chart-of-accounts.index', compact('accounts'));
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

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
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

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('chart-of-accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(ChartOfAccount $chartOfAccount)
    {
        $chartOfAccount->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('chart-of-accounts.index')->with('success', 'Account deleted successfully.');
    }
}
