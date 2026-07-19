<?php // FinancialReportController — serves financial report pages and their PDF versions. Periods are derived from posted journal entries, not from FinancialReport table.
namespace App\Http\Controllers\FinancialReporting;

use App\Http\Controllers\Controller;
use App\Models\AccountPayable\Payment;
use App\Models\AccountPayable\SupplierBill;
use App\Models\FinancialReporting\BudgetVsActual;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\Sales\SalesTransaction;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    public function income()
    {
        $data = $this->incomeData();

        return view('financial-reporting.reports.income', $data);
    }

    public function incomePdf()
    {
        return view('financial-reporting.pdf.income', $this->incomeData());
    }

    public function assets()
    {
        return view('financial-reporting.reports.assets', $this->assetsData());
    }

    public function assetsPdf()
    {
        return view('financial-reporting.pdf.assets', $this->assetsData());
    }

    public function liabilities()
    {
        return redirect()->route('reports.budget');
    }

    public function liabilitiesPdf()
    {
        return redirect()->route('reports.budget.pdf');
    }

    public function budget()
    {
        return view('financial-reporting.reports.budget', $this->budgetData());
    }

    public function budgetPdf()
    {
        return view('financial-reporting.pdf.budget', $this->budgetData());
    }

    public function cashflow()
    {
        return view('financial-reporting.reports.cashflow', $this->cashflowData());
    }

    public function cashflowPdf()
    {
        return view('financial-reporting.pdf.cashflow', $this->cashflowData());
    }

    private function getPeriods(): array
    {
        $jePeriods = JournalEntry::where('status', 'Posted')->get()
            ->groupBy(fn ($e) => $e->transaction_date->format('F Y'))->keys();
        $billPeriods = SupplierBill::whereNotNull('paid_at')->get()
            ->groupBy(fn ($e) => $e->paid_at->format('F Y'))->keys();
        $paymentPeriods = Payment::get()
            ->groupBy(fn ($e) => $e->payment_date->format('F Y'))->keys();
        $salesPeriods = SalesTransaction::get()
            ->groupBy(fn ($e) => $e->created_at->format('F Y'))->keys();
        return $jePeriods->merge($billPeriods)->merge($paymentPeriods)->merge($salesPeriods)
            ->unique()->sortDesc()->values()->toArray();
    }

    private function parsePeriod(?string $period): array
    {
        if (!$period) return [null, null];
        $start = Carbon::parse('first day of ' . $period);
        $end = Carbon::parse('last day of ' . $period);
        return [$start, $end];
    }

    private function incomeData(): array
    {
        $periods = $this->getPeriods();
        $selectedPeriod = request('period');
        if (!$selectedPeriod || !in_array($selectedPeriod, $periods)) {
            $selectedPeriod = $periods[0] ?? null;
        }

        [$start, $end] = $this->parsePeriod($selectedPeriod);

        $revenue = [];
        $expenses = [];
        $trialBalance = [];

        // All Revenue accounts (including zero-balance)
        $revenue = \App\Models\GeneralLedger\ChartOfAccount::where('type', 'Revenue')->orderBy('account_name')->get()
            ->map(function ($a) use ($start, $end) {
                $totals = \App\Models\GeneralLedger\JournalEntryLine::select(
                        \DB::raw('COALESCE(SUM(credit),0) as total'))
                    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
                    ->where('journal_entry_lines.account_id', $a->account_id)
                    ->where('journal_entries.status', 'Posted')
                    ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
                    ->first();
                return ['label' => $a->account_name, 'amount' => (float) $totals->total];
            })->filter(fn ($r) => $r['amount'] > 0)->values()->toArray();

        // All Expense accounts (including zero-balance)
        $expenses = \App\Models\GeneralLedger\ChartOfAccount::where('type', 'Expense')->orderBy('account_name')->get()
            ->map(function ($a) use ($start, $end) {
                $totals = \App\Models\GeneralLedger\JournalEntryLine::select(
                        \DB::raw('COALESCE(SUM(debit),0) as total'))
                    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
                    ->where('journal_entry_lines.account_id', $a->account_id)
                    ->where('journal_entries.status', 'Posted')
                    ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
                    ->first();
                return ['label' => $a->account_name, 'amount' => (float) $totals->total];
            })->filter(fn ($r) => $r['amount'] > 0)->values()->toArray();

        // Trial balance — all accounts, even zero-balance
        $trialBalance = \App\Models\GeneralLedger\ChartOfAccount::orderBy('account_name')->get()
            ->map(function ($a) use ($start, $end) {
                $totals = \App\Models\GeneralLedger\JournalEntryLine::select(
                        \DB::raw('COALESCE(SUM(debit),0) as debit_total'),
                        \DB::raw('COALESCE(SUM(credit),0) as credit_total'))
                    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
                    ->where('journal_entry_lines.account_id', $a->account_id)
                    ->where('journal_entries.status', 'Posted')
                    ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
                    ->first();
                return [
                    'account' => $a->account_name,
                    'debit'   => (float) $totals->debit_total,
                    'credit'  => (float) $totals->credit_total,
                ];
            })->toArray();

        return [
            'periods'        => $periods,
            'selectedPeriod' => $selectedPeriod,
            'revenue'        => $revenue,
            'expenses'       => $expenses,
            'trialBalance'   => $trialBalance,
        ];
    }

    private function assetsData(): array
    {
        $periods = $this->getPeriods();
        $selectedPeriod = request('period');
        if (!$selectedPeriod || !in_array($selectedPeriod, $periods)) {
            $selectedPeriod = $periods[0] ?? null;
        }

        [$start, $end] = $this->parsePeriod($selectedPeriod);

        $assets      = [];
        $liabilities = [];
        $equity      = [];

        // All Asset/Liability/Equity accounts
        $bsAccounts = \App\Models\GeneralLedger\ChartOfAccount::whereIn('type', ['Asset', 'Liability', 'Equity'])
            ->orderBy('type')
            ->orderBy('account_name')
            ->get();

        foreach ($bsAccounts as $a) {
            $totals = \App\Models\GeneralLedger\JournalEntryLine::select(
                    \DB::raw('COALESCE(SUM(debit),0) as debit_total'),
                    \DB::raw('COALESCE(SUM(credit),0) as credit_total'))
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
                ->where('journal_entry_lines.account_id', $a->account_id)
                ->where('journal_entries.status', 'Posted')
                ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
                ->first();

            $balance = $a->normal_balance === 'Credit'
                ? (float) $totals->credit_total - (float) $totals->debit_total
                : (float) $totals->debit_total - (float) $totals->credit_total;

            $item = ['label' => $a->account_name, 'amount' => max($balance, 0)];
            match ($a->type) {
                'Asset'     => $assets[] = $item,
                'Liability' => $liabilities[] = $item,
                'Equity'    => $equity[] = $item,
            };
        }

        // Compute net income → Retained Earnings to balance the equation
        $totalRevenue = (float) \App\Models\GeneralLedger\JournalEntryLine::join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.account_id')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
            ->where('chart_of_accounts.type', 'Revenue')
            ->where('journal_entries.status', 'Posted')
            ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
            ->sum('credit');

        $totalExpenses = (float) \App\Models\GeneralLedger\JournalEntryLine::join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.account_id')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
            ->where('chart_of_accounts.type', 'Expense')
            ->where('journal_entries.status', 'Posted')
            ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
            ->sum('debit');

        $netIncome = $totalRevenue - $totalExpenses;

        if ($netIncome > 0) {
            $equity[] = ['label' => 'Retained Earnings', 'amount' => $netIncome];
        } elseif ($netIncome < 0) {
            $equity[] = ['label' => 'Retained Earnings (Deficit)', 'amount' => abs($netIncome)];
        }

        return [
            'assets'          => $assets,
            'liabilities'     => $liabilities,
            'equity'          => $equity,
            'periods'         => $periods,
            'selectedPeriod'  => $selectedPeriod,
            'hasData'         => true,
        ];
    }

    private function budgetData(): array
    {
        $periods = $this->getPeriods();
        $selectedPeriod = request('period');
        if (!$selectedPeriod || !in_array($selectedPeriod, $periods)) {
            $selectedPeriod = $periods[0] ?? null;
        }

        [$start, $end] = $this->parsePeriod($selectedPeriod);

        $budgetRows = BudgetVsActual::when($start && $end, fn ($q) => $q
                ->whereBetween('report_period_start', [$start, $end])
                ->orWhereBetween('report_period_end', [$start, $end])
            )
            ->orderBy('budget_actual_id')
            ->get();

        if ($budgetRows->isEmpty()) {
            return [
                'periods'        => $periods,
                'selectedPeriod' => $selectedPeriod,
                'budgetVsActual' => [],
            ];
        }

        $accountNames = $budgetRows->pluck('account_name');
        $coaAccounts = \App\Models\GeneralLedger\ChartOfAccount::whereIn('account_name', $accountNames)
            ->get()
            ->keyBy('account_name');

        // Get actuals from journal entries for the same accounts
        $actuals = \App\Models\GeneralLedger\JournalEntryLine::select('chart_of_accounts.account_name',
                'chart_of_accounts.normal_balance',
                \DB::raw('COALESCE(SUM(journal_entry_lines.debit), 0) as debit_total'),
                \DB::raw('COALESCE(SUM(journal_entry_lines.credit), 0) as credit_total'))
            ->join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.account_id')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
            ->where('journal_entries.status', 'Posted')
            ->whereIn('chart_of_accounts.account_name', $accountNames)
            ->when($start && $end, fn ($q) => $q->whereBetween('journal_entries.transaction_date', [$start, $end]))
            ->groupBy('chart_of_accounts.account_name', 'chart_of_accounts.normal_balance')
            ->get()
            ->keyBy('account_name');

        return [
            'periods'        => $periods,
            'selectedPeriod' => $selectedPeriod,
            'budgetVsActual' => $budgetRows->map(function ($row) use ($actuals, $coaAccounts) {
                $accountName = $row->account_name;
                $budgetAmount = (float) $row->budget_amount;
                $coa = $coaAccounts->get($accountName);

                $actualEntry = $actuals->get($accountName);
                if ($actualEntry) {
                    $debits = (float) $actualEntry->debit_total;
                    $credits = (float) $actualEntry->credit_total;
                    if ($coa && $coa->normal_balance === 'Credit') {
                        $actualAmount = $credits - $debits;
                    } else {
                        $actualAmount = $debits - $credits;
                    }
                    $actualAmount = max($actualAmount, 0);
                } else {
                    $actualAmount = (float) $row->actual_amount;
                }

                $variance = $actualAmount - $budgetAmount;

                return [
                    'account' => $accountName,
                    'budget'  => $budgetAmount,
                    'actual'  => $actualAmount,
                    'status'  => match (true) {
                        $variance > 0 && $variance / max($budgetAmount, 1) < 0.05 => 'slightly_over',
                        $variance > 0 => 'over',
                        $variance < 0 => 'under',
                        default       => 'on_budget',
                    },
                ];
            })->toArray(),
        ];
    }

    private function cashflowData(): array
    {
        $periods = $this->getPeriods();
        $selectedPeriod = request('period');
        if (!$selectedPeriod || !in_array($selectedPeriod, $periods)) {
            $selectedPeriod = $periods[0] ?? null;
        }

        [$start, $end] = $this->parsePeriod($selectedPeriod);

        $cashAccountIds = \App\Models\GeneralLedger\ChartOfAccount::where('account_name', 'like', 'Cash%')
            ->pluck('account_id');

        // Cash In = debit lines to Cash accounts where the other side is NOT Cash (internal transfer)
        $cashInLines = collect();
        if ($cashAccountIds->isNotEmpty()) {
            $cashInLines = \App\Models\GeneralLedger\JournalEntryLine::selectRaw('coa.account_name, SUM(jel.debit) as total')
                ->from('journal_entry_lines as jel')
                ->join('chart_of_accounts as coa', 'jel.account_id', '=', 'coa.account_id')
                ->join('journal_entries as je', 'jel.journal_entry_id', '=', 'je.journal_entry_id')
                ->whereIn('jel.account_id', $cashAccountIds)
                ->where('jel.debit', '>', 0)
                ->where('je.status', 'Posted')
                ->when($start && $end, fn ($q) => $q->whereBetween('je.transaction_date', [$start, $end]))
                ->whereExists(function ($q) use ($cashAccountIds) {
                    $q->selectRaw(1)
                      ->from('journal_entry_lines as jel2')
                      ->whereColumn('jel2.journal_entry_id', 'jel.journal_entry_id')
                      ->whereNotIn('jel2.account_id', $cashAccountIds);
                })
                ->groupBy('coa.account_name')
                ->get()
                ->map(fn ($r) => ['label' => $r->account_name . ' (received)', 'amount' => (float) $r->total]);
        }

        // Cash Out = credit lines to Cash accounts where the debit side is an Expense/AP
        $cashOutLines = collect();
        if ($cashAccountIds->isNotEmpty()) {
            $cashOutLines = \App\Models\GeneralLedger\JournalEntryLine::selectRaw('coa.account_name, SUM(jel.credit) as total')
                ->from('journal_entry_lines as jel')
                ->join('chart_of_accounts as coa', 'jel.account_id', '=', 'coa.account_id')
                ->join('journal_entries as je', 'jel.journal_entry_id', '=', 'je.journal_entry_id')
                ->whereIn('jel.account_id', $cashAccountIds)
                ->where('jel.credit', '>', 0)
                ->where('je.status', 'Posted')
                ->when($start && $end, fn ($q) => $q->whereBetween('je.transaction_date', [$start, $end]))
                ->whereExists(function ($q) use ($cashAccountIds) {
                    $q->selectRaw(1)
                      ->from('journal_entry_lines as jel2')
                      ->join('chart_of_accounts as coa2', 'jel2.account_id', '=', 'coa2.account_id')
                      ->whereColumn('jel2.journal_entry_id', 'jel.journal_entry_id')
                      ->whereNotIn('jel2.account_id', $cashAccountIds)
                      ->whereIn('coa2.type', ['Expense', 'Liability']);
                })
                ->groupBy('coa.account_name')
                ->get()
                ->map(fn ($r) => ['label' => $r->account_name . ' (paid)', 'amount' => (float) $r->total]);
        }

        // Fallback: if no Cash accounts exist but AP/AR data exists, show from there
        if ($cashAccountIds->isEmpty()) {
            $paidBills = SupplierBill::where('status', 'Paid')
                ->when($start && $end, fn ($q) => $q->whereBetween('paid_at', [$start, $end]))
                ->get();
            foreach ($paidBills as $bill) {
                $cashOutLines->push(['label' => 'Supplier Payment' . ($bill->po_no ? " ({$bill->po_no})" : ''), 'amount' => (float) $bill->amount]);
            }

            $paidSales = SalesTransaction::where('is_posted_to_finance', true)
                ->when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
                ->get();
            foreach ($paidSales as $s) {
                $cashInLines->push(['label' => 'Sales (' . ($s->payment_method ?? 'Unknown') . ')', 'amount' => (float) $s->total_amount]);
            }
        }

        // Collapse duplicates
        $cashInLines = $cashInLines->groupBy('label')->map(fn ($g) => [
            'label' => $g->first()['label'], 'amount' => $g->sum('amount'),
        ])->values();
        $cashOutLines = $cashOutLines->groupBy('label')->map(fn ($g) => [
            'label' => $g->first()['label'], 'amount' => $g->sum('amount'),
        ])->values();

        $totalCashIn  = $cashInLines->sum('amount');
        $totalCashOut = $cashOutLines->sum('amount');
        $netCashFlow  = $totalCashIn - $totalCashOut;

        $beginningCash = 0;
        if ($start && $cashAccountIds->isNotEmpty()) {
            $beginningCash = (float) \App\Models\GeneralLedger\JournalEntryLine::whereIn('account_id', $cashAccountIds)
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.journal_entry_id')
                ->where('journal_entries.status', 'Posted')
                ->where('journal_entries.transaction_date', '<', $start)
                ->selectRaw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) as balance')
                ->value('balance');
        }

        return [
            'periods'        => $periods,
            'selectedPeriod' => $selectedPeriod,
            'periodLabel'    => $selectedPeriod ?? 'All',
            'cashInLines'    => $cashInLines->toArray(),
            'cashOutLines'   => $cashOutLines->toArray(),
            'totalCashIn'    => $totalCashIn,
            'totalCashOut'   => $totalCashOut,
            'netCashFlow'    => $netCashFlow,
            'beginningCash'  => $beginningCash,
            'endingCash'     => $beginningCash + $netCashFlow,
            'hasData'        => true,
        ];
    }
}