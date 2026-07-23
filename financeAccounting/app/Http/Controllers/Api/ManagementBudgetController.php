<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinancialReporting\BudgetVsActual;
use App\Models\GeneralLedger\ChartOfAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManagementBudgetController extends Controller
{
    private function verifyKey(Request $request): bool
    {
        return $request->header('X-API-Key') === config('app.management_api_key');
    }

    public function store(Request $request)
    {
        if (!$this->verifyKey($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'account_code' => 'required|string|exists:chart_of_accounts,account_code',
            'budget_amount' => 'required|numeric|min:0',
            'period' => 'required|string',
        ]);

        $account = ChartOfAccount::where('account_code', $data['account_code'])->firstOrFail();
        $start = Carbon::createFromFormat('F Y', $data['period'])->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $budget = BudgetVsActual::create([
            'account_id' => $account->account_id,
            'account_name' => $account->account_name,
            'budget_amount' => $data['budget_amount'],
            'actual_amount' => 0,
            'report_period_start' => $start,
            'report_period_end' => $end,
        ]);

        return response()->json(['message' => 'Budget entry created', 'data' => $budget], 201);
    }

    public function index(Request $request)
    {
        if (!$this->verifyKey($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(BudgetVsActual::orderByDesc('report_period_start')->get());
    }

    public function destroy(Request $request, $id)
    {
        if (!$this->verifyKey($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $budget = BudgetVsActual::findOrFail($id);
        $budget->delete();

        return response()->json(['message' => 'Budget entry deleted']);
    }
}