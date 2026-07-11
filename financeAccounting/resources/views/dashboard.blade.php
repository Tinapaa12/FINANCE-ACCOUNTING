@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div x-data="dashboard()" x-init="initCharts()" class="space-y-6">
    <!-- KPI Cards -->
    <div class="grid grid-cols-4 gap-4">
        <x-kpi-card 
            label="Total Revenue" 
            value="₱{{ number_format($kpi['total_revenue'], 2) }}"
            change="-" 
            changeType="positive"
            iconBg="bg-green-100"
            iconColor="text-green-600"
            iconPath='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' />

        <x-kpi-card 
            label="Total Expenses" 
            value="₱{{ number_format($kpi['total_expenses'], 2) }}"
            change="-"
            changeType="negative"
            iconBg="bg-red-100"
            iconColor="text-red-500"
            iconPath='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>' />

        <x-kpi-card 
            label="Net Profit" 
            value="₱{{ number_format($kpi['net_profit'], 2) }}"
            change="-"
            :changeType="$kpi['net_profit'] >= 0 ? 'positive' : 'negative'"
            iconBg="bg-blue-100"
            iconColor="text-blue-500"
            iconPath='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>' />

        <x-kpi-card 
            label="Cash Balance" 
            value="₱{{ number_format($kpi['cash_balance'], 2) }}"
            change="-"
            changeType="neutral"
            iconBg="bg-purple-100"
            iconColor="text-purple-500"
            iconPath='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>' />
    </div>

    <!-- Charts & Sidebar -->
    <div class="grid grid-cols-12 gap-4">
        <!-- Charts Area -->
        <div class="col-span-9 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <!-- Cash Flow -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Cash Flow Overview</h3>
                    <div class="flex items-center gap-4 mb-2 text-xs">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span>Cash In</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span>Cash Out</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span>Net Cash Flow</span>
                    </div>
                    <div class="h-44">
                        <canvas id="cashFlowChart"></canvas>
                    </div>
                </div>

                <!-- Revenue vs Expenses -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Revenue vs Expenses</h3>
                    <div class="flex items-center gap-4 mb-2 text-xs">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span>Revenue</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span>Expenses</span>
                    </div>
                    <div class="h-44">
                        <canvas id="revenueExpensesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Journal Entries -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-900">Recent Journal Entries</h3>
                    <span class="text-xs text-gray-500">{{ $recentEntries->count() }} entries</span>
                </div>
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase py-2">Date</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase py-2">Reference</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase py-2">Description</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentEntries as $entry)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 text-sm text-gray-600">{{ $entry['date'] }}</td>
                            <td class="py-3 text-sm text-gray-900 font-medium">{{ $entry['reference'] }}</td>
                            <td class="py-3 text-sm text-gray-900">{{ $entry['description'] }}</td>
                            <td class="py-3">
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $entry['status'] === 'Posted' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $entry['status'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-sm text-gray-400">No journal entries yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    <a href="{{ route('journal-entries.index') }}" class="inline-flex items-center gap-1 px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600">
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-span-3 space-y-4">
            <!-- Accounts Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Accounts Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Chart of Accounts</span>
                        <span class="font-semibold text-gray-900">{{ $accountsSummary['total_accounts'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Journal Entries</span>
                        <span class="font-semibold text-gray-900">{{ \App\Models\JournalEntry::count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Active Accounts</span>
                        <span class="font-semibold text-gray-900">{{ $accountsSummary['active'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Inactive Accounts</span>
                        <span class="font-semibold text-gray-900">{{ $accountsSummary['inactive'] }}</span>
                    </div>
                    <hr class="my-2">
                    @foreach($accountTypeCounts as $type => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 capitalize">{{ $type }}s</span>
                        <span class="font-semibold text-gray-900">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Financial Alerts -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Financial Alerts</h3>
                <div class="space-y-2">
                    @forelse($alerts as $alert)
                    <div class="flex items-center gap-2 p-2 bg-{{ $alert['color'] }}-50 rounded-lg border border-{{ $alert['color'] }}-100">
                        <span class="w-2 h-2 rounded-full bg-{{ $alert['color'] }}-500 flex-shrink-0"></span>
                        <span class="text-xs text-{{ $alert['color'] }}-700 font-medium">{{ $alert['text'] }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400">No alerts.</p>
                    @endforelse
                </div>
            </div>

            <!-- Link to Chart of Accounts -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Quick Links</h3>
                <div class="space-y-2">
                    <a href="{{ route('chart-of-accounts.index') }}" class="flex items-center gap-2 p-3 bg-blue-50 rounded-lg border border-blue-100 hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="text-sm font-medium text-blue-700">Chart of Accounts</span>
                    </a>
                    <a href="{{ route('journal-entries.index') }}" class="flex items-center gap-2 p-3 bg-purple-50 rounded-lg border border-purple-100 hover:bg-purple-100 transition-colors">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span class="text-sm font-medium text-purple-700">Journal Entries</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const chartData = @json($chartData);

    let cashFlowChartInstance = null;
    let revenueExpensesChartInstance = null;

    function dashboard() {
        return {
            chartsInitialized: false,

            initCharts() {
                if (this.chartsInitialized) return;
                this.$nextTick(() => {
                    const cashCanvas = document.getElementById('cashFlowChart');
                    const revCanvas = document.getElementById('revenueExpensesChart');
                    if (!cashCanvas || !revCanvas) return;

                    if (cashFlowChartInstance) cashFlowChartInstance.destroy();
                    if (revenueExpensesChartInstance) revenueExpensesChartInstance.destroy();

                    const labels = chartData.cash_flow.map(d => d.month);

                    cashFlowChartInstance = new Chart(cashCanvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                { label: 'Cash In', data: chartData.cash_flow.map(d => d.cash_in), backgroundColor: '#22c55e', borderRadius: 3, barPercentage: 0.6, categoryPercentage: 0.8 },
                                { label: 'Cash Out', data: chartData.cash_flow.map(d => d.cash_out), backgroundColor: '#ef4444', borderRadius: 3, barPercentage: 0.6, categoryPercentage: 0.8 },
                                { type: 'line', label: 'Net Cash Flow', data: chartData.cash_flow.map(d => d.net), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', tension: 0.4, pointRadius: 3, pointBackgroundColor: '#3b82f6', borderWidth: 2, fill: true }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 800, easing: 'easeOutQuart' },
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                                y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 }, callback: v => v + 'k' } }
                            }
                        }
                    });

                    revenueExpensesChartInstance = new Chart(revCanvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: chartData.revenue_expenses.map(d => d.month),
                            datasets: [
                                { label: 'Revenue', data: chartData.revenue_expenses.map(d => d.revenue), backgroundColor: '#3b82f6', borderRadius: 3, barPercentage: 0.6, categoryPercentage: 0.8 },
                                { label: 'Expenses', data: chartData.revenue_expenses.map(d => d.expenses), backgroundColor: '#ef4444', borderRadius: 3, barPercentage: 0.6, categoryPercentage: 0.8 }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 800, easing: 'easeOutQuart' },
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                                y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 }, callback: v => v + 'k' } }
                            }
                        }
                    });

                    this.chartsInitialized = true;
                });
            }
        }
    }
</script>
@endsection