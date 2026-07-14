<?php // DashboardController — serves the main dashboard view. Aggregates KPI data, recent journal entries, account summaries, chart data, financial alerts, and account type counts via DashboardService.
namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function index()
    {
        $kpi = $this->dashboardService->getKpiData();
        $recentEntries = $this->dashboardService->getRecentJournalEntries();
        $accountsSummary = $this->dashboardService->getAccountsSummary();
        $chartData = $this->dashboardService->getChartData();
        $alerts = $this->dashboardService->getFinancialAlerts();
        $accountTypeCounts = $this->dashboardService->getAccountTypeCounts();

        return view('dashboard', compact(
            'kpi',
            'recentEntries',
            'accountsSummary',
            'chartData',
            'alerts',
            'accountTypeCounts'
        ));
    }
}
