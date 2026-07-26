<?php // DashboardController — serves the main dashboard view. Aggregates KPI data, recent journal entries, account summaries, chart data, financial alerts, account type counts, and AP/AR summary metrics.
namespace App\Http\Controllers;

use App\Services\AccountPayableService;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly AccountPayableService $accountPayableService
    ) {}

    public function index()
    {
        $kpi = $this->dashboardService->getKpiData();
        $recentEntries = $this->dashboardService->getRecentJournalEntries();
        $accountsSummary = $this->dashboardService->getAccountsSummary();
        $chartData = $this->dashboardService->getChartData();
        $alerts = $this->dashboardService->getFinancialAlerts();
        $accountTypeCounts = $this->dashboardService->getAccountTypeCounts();

        $apMetrics = $this->accountPayableService->getDashboardMetrics();
        $arMetrics = $this->dashboardService->getArMetrics();

        return view('dashboard.index', compact(
            'kpi',
            'recentEntries',
            'accountsSummary',
            'chartData',
            'alerts',
            'accountTypeCounts',
            'apMetrics',
            'arMetrics'
        ));
    }
}
