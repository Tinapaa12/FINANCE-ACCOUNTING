<?php // TransactionCostController — provides a Blade form simulating the Supply Chain module that posts to the existing Supplier Bills API.
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\GeneralLedger\ChartOfAccount;

class TransactionCostController extends Controller
{
    public function create()
    {
        $expenseAccounts = ChartOfAccount::where('type', 'Expense')->where('status', 'Active')->orderBy('account_code')->get();
        return view('supply-chain.create', compact('expenseAccounts'));
    }
}
