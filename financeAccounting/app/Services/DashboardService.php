<?php // DashboardService — encapsulates business logic for the dashboard. Computes KPI data, recent journal entries, account summaries, chart data, and alerts.
namespace App\Services;

use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\GeneralLedger\JournalEntryLine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getKpiData(): array
    {
        $revenue = $this->getTotalByAccountType('Revenue', 'credit');
        $expenses = $this->getTotalByAccountType('Expense', 'debit');
        $netProfit = $revenue - $expenses;
        $cashBalance = $this->getCashBalance();

        return [
            'total_revenue' => $revenue,
            'total_expenses' => $expenses,
            'net_profit' => $netProfit,
            'cash_balance' => $cashBalance,
        ];
    }

    public function getRecentJournalEntries(int $limit = 5): Collection
    {
        return JournalEntry::with(['lines.account', 'salesTransaction'])
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn(JournalEntry $entry) => [
                'date' => $entry->transaction_date->format('F d, Y'),
                'reference' => $entry->salesTransaction?->order_no ?? $entry->reference_no,
                'description' => $entry->description,
                'status' => $entry->status,
            ]);
    }

    public function getAccountsSummary(): array
    {
        $total = ChartOfAccount::count();
        $active = ChartOfAccount::where('status', 'Active')->count();
        $inactive = ChartOfAccount::where('status', 'Inactive')->count();

        return [
            'total_accounts' => $total,
            'active' => $active,
            'inactive' => $inactive,
        ];
    }

    public function getChartData(): array
    {
        $months = collect(range(1, 12))->map(fn(int $m) => [
            'month' => date('M', mktime(0, 0, 0, $m, 1)),
            'revenue' => $this->getMonthlyTotal($m, 'Revenue', 'credit'),
            'expenses' => $this->getMonthlyTotal($m, 'Expense', 'debit'),
        ]);

        return [
            'cash_flow' => $this->getCashFlowChartData(),
            'revenue_expenses' => $months,
        ];
    }

    public function getFinancialAlerts(): Collection
    {
        $alerts = collect();

        $draftCount = JournalEntry::where('status', 'Draft')->count();
        if ($draftCount > 0) {
            $alerts->push([
                'color' => 'yellow',
                'text' => "{$draftCount} Draft Journal " . str('Entry')->plural($draftCount),
            ]);
        }

        $totalAccounts = ChartOfAccount::count();
        if ($totalAccounts === 0) {
            $alerts->push([
                'color' => 'red',
                'text' => 'No chart of accounts configured',
            ]);
        }

        $unbalanced = JournalEntry::with('lines')
            ->get()
            ->filter(fn(JournalEntry $entry) => !$entry->isBalanced());

        if ($unbalanced->isNotEmpty()) {
            $alerts->push([
                'color' => 'red',
                'text' => $unbalanced->count() . ' unbalanced journal ' . str('entry')->plural($unbalanced->count()),
            ]);
        }

        if ($alerts->isEmpty()) {
            $alerts->push([
                'color' => 'green',
                'text' => 'All accounts are in good standing',
            ]);
        }

        return $alerts;
    }

    public function getAccountTypeCounts(): Collection
    {
        return ChartOfAccount::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type');
    }

    private function getTotalByAccountType(string $type, string $column): float
    {
        return (float) JournalEntryLine::whereHas('account', fn($q) => $q->where('type', $type))
            ->whereHas('journalEntry', fn($q) => $q->where('status', 'Posted'))
            ->sum($column);
    }

    private function getCashBalance(): float
    {
        $debits = (float) JournalEntryLine::whereHas('account', fn($q) => $q->where('type', 'Asset'))
            ->whereHas('journalEntry', fn($q) => $q->where('status', 'Posted'))
            ->sum('debit');

        $credits = (float) JournalEntryLine::whereHas('account', fn($q) => $q->where('type', 'Asset'))
            ->whereHas('journalEntry', fn($q) => $q->where('status', 'Posted'))
            ->sum('credit');

        return $debits - $credits;
    }

    private function getMonthlyTotal(int $month, string $type, string $column): float
    {
        $year = now()->year;
        return (float) JournalEntryLine::whereHas('account', fn($q) => $q->where('type', $type))
            ->whereHas('journalEntry', fn($q) => $q->where('status', 'Posted')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month))
            ->sum($column);
    }

    private function getCashFlowChartData(): array
    {
        $year = now()->year;
        $months = [];

        foreach (range(1, 12) as $m) {
            $cashIn = (float) JournalEntryLine::whereHas('account', fn($q) => $q->where('type', 'Revenue'))
                ->whereHas('journalEntry', fn($q) => $q->where('status', 'Posted')
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $m))
                ->sum('credit');

            $cashOut = (float) JournalEntryLine::whereHas('account', fn($q) => $q->where('type', 'Expense'))
                ->whereHas('journalEntry', fn($q) => $q->where('status', 'Posted')
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $m))
                ->sum('debit');

            $months[] = [
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'cash_in' => $cashIn,
                'cash_out' => $cashOut,
                'net' => $cashIn - $cashOut,
            ];
        }

        return $months;
    }
}
