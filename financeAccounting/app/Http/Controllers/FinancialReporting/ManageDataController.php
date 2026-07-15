<?php
namespace App\Http\Controllers\FinancialReporting;

use App\Http\Controllers\Controller;
use App\Models\FinancialReporting\BudgetVsActual;
use App\Models\FinancialReporting\FinancialReport;
use App\Models\FinancialReporting\TaxRecord;
use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\GeneralLedger\JournalEntryLine;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ManageDataController extends Controller
{
    private function getReportPeriods()
    {
        $glPeriods = JournalEntry::where('status', 'Posted')
            ->get()
            ->groupBy(fn ($e) => $e->transaction_date->format('F Y'))
            ->keys();

        $frPeriods = FinancialReport::select('report_period_start')->distinct()->get()
            ->map(fn ($r) => Carbon::parse($r->report_period_start)->format('F Y'));

        return $glPeriods->merge($frPeriods)->unique()->sortDesc()->values();
    }

    public function index()
    {
        return view('financial-reporting.manage.index', [
            'reportPeriods' => $this->getReportPeriods(),
        ]);
    }

    private function findOrCreateAccount(string $name, string $type, string $normalBalance): ChartOfAccount
    {
        return ChartOfAccount::firstOrCreate(
            ['account_name' => $name],
            [
                'account_code'   => '9' . str_pad((intval(ChartOfAccount::max('account_code') ?? 9000) + 1), 4, '0', STR_PAD_LEFT),
                'normal_balance' => $normalBalance,
                'type'           => $type,
                'status'         => 'Active',
            ]
        );
    }

    private function getCashAccount(): ?ChartOfAccount
    {
        return ChartOfAccount::where('account_name', 'like', 'Cash%')->first();
    }

    private function postJournalEntry(string $period, string $description, array $lines): JournalEntry
    {
        $date = Carbon::parse('first day of ' . $period);

        $entry = JournalEntry::create([
            'transaction_date' => $date,
            'reference_no'     => 'JE-MANUAL-' . now()->format('YmdHis'),
            'description'      => $description,
            'status'           => 'Posted',
        ]);

        foreach ($lines as $line) {
            JournalEntryLine::create([
                'journal_entry_id' => $entry->journal_entry_id,
                'account_id'       => $line['account_id'],
                'description'      => $description,
                'debit'            => $line['debit'],
                'credit'           => $line['credit'],
            ]);
        }

        return $entry;
    }

    // === Quick-entry Income Statement ===

    public function storeIncome(Request $request)
    {
        $data = $request->validate([
            'account_name' => 'required|string|max:255',
            'category'     => 'required|in:Revenue,Expense',
            'amount'       => 'required|numeric|min:0.01',
            'period'       => 'required|string|max:255',
        ]);

        $cash = $this->getCashAccount();
        if (!$cash) {
            return redirect()->route('reports.manage', ['tab' => 'income'])
                ->with('success', 'Error: No Cash account found. Create one in Chart of Accounts first.');
        }

        $isRevenue = $data['category'] === 'Revenue';
        $account = $this->findOrCreateAccount($data['account_name'], $data['category'], $isRevenue ? 'Credit' : 'Debit');
        $amount = (float) $data['amount'];

        if ($isRevenue) {
            // DR Cash, CR Revenue
            $this->postJournalEntry($data['period'], 'Revenue: ' . $data['account_name'], [
                ['account_id' => $cash->account_id, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $account->account_id, 'debit' => 0, 'credit' => $amount],
            ]);
        } else {
            // DR Expense, CR Cash
            $this->postJournalEntry($data['period'], 'Expense: ' . $data['account_name'], [
                ['account_id' => $account->account_id, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $cash->account_id, 'debit' => 0, 'credit' => $amount],
            ]);
        }

        return redirect()->route('reports.manage', ['tab' => 'income'])->with('success', $data['category'] . ' entry added. It will appear on reports immediately.');
    }

    // === Quick-entry Balance Sheet ===

    public function storeBalance(Request $request)
    {
        $data = $request->validate([
            'account_name' => 'required|string|max:255',
            'section'      => 'required|in:Asset,Liability,Equity',
            'amount'       => 'required|numeric|min:0.01',
            'period'       => 'required|string|max:255',
        ]);

        $cash = $this->getCashAccount();
        if (!$cash) {
            return redirect()->route('reports.manage', ['tab' => 'balance'])
                ->with('success', 'Error: No Cash account found. Create one in Chart of Accounts first.');
        }

        $normalBalance = $data['section'] === 'Asset' ? 'Debit' : 'Credit';
        $account = $this->findOrCreateAccount($data['account_name'], $data['section'], $normalBalance);
        $amount = (float) $data['amount'];

        if ($data['section'] === 'Asset') {
            // DR Asset, CR Cash (buying asset)
            $this->postJournalEntry($data['period'], 'Asset: ' . $data['account_name'], [
                ['account_id' => $account->account_id, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $cash->account_id, 'debit' => 0, 'credit' => $amount],
            ]);
        } elseif ($data['section'] === 'Liability') {
            // DR Cash, CR Liability (receiving loan)
            $this->postJournalEntry($data['period'], 'Liability: ' . $data['account_name'], [
                ['account_id' => $cash->account_id, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $account->account_id, 'debit' => 0, 'credit' => $amount],
            ]);
        } else {
            // DR Cash, CR Equity (owner investment)
            $this->postJournalEntry($data['period'], 'Equity: ' . $data['account_name'], [
                ['account_id' => $cash->account_id, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $account->account_id, 'debit' => 0, 'credit' => $amount],
            ]);
        }

        return redirect()->route('reports.manage', ['tab' => 'balance'])->with('success', 'Balance sheet entry added. It will appear on reports immediately.');
    }

    // === Quick-entry Cash Flow ===

    public function storeCashFlow(Request $request)
    {
        $data = $request->validate([
            'flow_type'    => 'required|in:Cash In,Cash Out',
            'account_name' => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'period'       => 'required|string|max:255',
        ]);

        $cash = $this->getCashAccount();
        if (!$cash) {
            return redirect()->route('reports.manage', ['tab' => 'cashflow'])
                ->with('success', 'Error: No Cash account found. Create one in Chart of Accounts first.');
        }

        // Use a temporary account name for the other side
        $otherAcct = $this->findOrCreateAccount($data['account_name'], 'Asset', 'Debit');
        $amount = (float) $data['amount'];

        if ($data['flow_type'] === 'Cash In') {
            // DR Cash, CR Other
            $this->postJournalEntry($data['period'], 'Cash In: ' . $data['account_name'], [
                ['account_id' => $cash->account_id, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $otherAcct->account_id, 'debit' => 0, 'credit' => $amount],
            ]);
        } else {
            // DR Other, CR Cash
            $this->postJournalEntry($data['period'], 'Cash Out: ' . $data['account_name'], [
                ['account_id' => $otherAcct->account_id, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $cash->account_id, 'debit' => 0, 'credit' => $amount],
            ]);
        }

        return redirect()->route('reports.manage', ['tab' => 'cashflow'])->with('success', 'Cash flow entry added. It will appear on reports immediately.');
    }

    // === Tax Records ===

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

    // === Budget vs Actual ===

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
}
