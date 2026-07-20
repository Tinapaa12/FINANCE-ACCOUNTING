<?php
namespace App\Http\Controllers\FinancialReporting;

use App\Http\Controllers\Controller;
use App\Models\AccountPayable\Payment;
use App\Models\AccountPayable\SupplierBill;
use App\Models\FinancialReporting\BudgetVsActual;
use App\Models\FinancialReporting\TaxRecord;
use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\Sales\SalesTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ManageDataController extends Controller
{
    public function index()
    {
        $jePeriods = JournalEntry::where('status', 'Posted')->get()
            ->groupBy(fn ($e) => $e->transaction_date->format('F Y'))->keys();
        $billPeriods = SupplierBill::whereNotNull('paid_at')->get()
            ->groupBy(fn ($e) => $e->paid_at->format('F Y'))->keys();
        $paymentPeriods = Payment::get()
            ->groupBy(fn ($e) => $e->payment_date->format('F Y'))->keys();
        $salesPeriods = SalesTransaction::get()
            ->groupBy(fn ($e) => $e->created_at->format('F Y'))->keys();
        $reportPeriods = $jePeriods->merge($billPeriods)->merge($paymentPeriods)->merge($salesPeriods)
            ->unique()->sortDesc()->values();

        return view('financial-reporting.manage.index', [
            'reportPeriods' => $reportPeriods,
            'accounts' => ChartOfAccount::where('status', 'Active')->orderBy('account_code')->get(),
        ]);
    }

    public function storeBudget(Request $request)
    {
        $data = $request->validate([
            'account_id'    => 'required|exists:chart_of_accounts,account_id',
            'budget_amount' => 'required|numeric|min:0',
            'actual_amount' => 'nullable|numeric|min:0',
            'tax_period'    => 'required|string|max:255',
        ]);

        $account = ChartOfAccount::findOrFail($data['account_id']);
        $start = Carbon::createFromFormat('F Y', $data['tax_period'])->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        BudgetVsActual::create([
            'account_id'          => $account->account_id,
            'account_name'        => $account->account_name,
            'budget_amount'       => $data['budget_amount'],
            'actual_amount'       => $data['actual_amount'] ?? 0,
            'report_period_start' => $start,
            'report_period_end'   => $end,
        ]);

        return redirect()->route('reports.manage', ['tab' => 'budget'])->with('success', 'Budget entry added.');
    }

    public function updateBudgetActual(Request $request, BudgetVsActual $budgetVsActual)
    {
        $data = $request->validate([
            'actual_amount' => 'required|numeric|min:0',
        ]);

        $budgetVsActual->update(['actual_amount' => $data['actual_amount']]);

        return redirect()->route('reports.manage', ['tab' => 'budget'])->with('success', 'Actual amount updated.');
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
