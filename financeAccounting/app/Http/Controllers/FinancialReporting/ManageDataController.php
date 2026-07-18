<?php
namespace App\Http\Controllers\FinancialReporting;

use App\Http\Controllers\Controller;
use App\Models\FinancialReporting\BudgetVsActual;
use App\Models\FinancialReporting\TaxRecord;
use App\Models\GeneralLedger\JournalEntry;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ManageDataController extends Controller
{
    public function index()
    {
        return view('financial-reporting.manage.index', [
            'reportPeriods' => JournalEntry::where('status', 'Posted')
                ->get()
                ->groupBy(fn ($e) => $e->transaction_date->format('F Y'))
                ->keys()
                ->sortDesc()
                ->values(),
        ]);
    }

    public function storeBudget(Request $request)
    {
        $data = $request->validate([
            'account_name'  => 'required|string|max:255',
            'budget_amount' => 'required|numeric|min:0',
            'tax_period'    => 'required|string|max:255',
        ]);

        $start = Carbon::createFromFormat('F Y', $data['tax_period'])->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        BudgetVsActual::create([
            'account_name'        => $data['account_name'],
            'budget_amount'       => $data['budget_amount'],
            'actual_amount'       => 0,
            'report_period_start' => $start,
            'report_period_end'   => $end,
        ]);

        return redirect()->route('reports.manage', ['tab' => 'budget'])->with('success', 'Budget entry added.');
    }

    public function destroyBudget(BudgetVsActual $budgetVsActual)
    {
        $budgetVsActual->delete();
        return redirect()->route('reports.manage', ['tab' => 'budget'])->with('success', 'Budget entry deleted.');
    }

    public function storeTaxRecord(Request $request)
    {
        $data = $request->validate([
            'reference_type' => 'required|string|max:255',
            'reference_id'   => 'required|integer',
            'tax_type'       => 'required|string|max:255',
            'taxable_amount' => 'required|numeric|min:0',
            'tax_rate'       => 'required|numeric|min:0',
            'tax_period'     => 'required|string|max:255',
            'filing_status'  => 'required|in:paid,filed,pending',
        ]);

        TaxRecord::create($data);

        return redirect()->route('reports.manage', ['tab' => 'tax'])->with('success', 'Tax record added.');
    }

    public function destroyTaxRecord(TaxRecord $taxRecord)
    {
        $taxRecord->delete();
        return redirect()->route('reports.manage', ['tab' => 'tax'])->with('success', 'Tax record deleted.');
    }
}
