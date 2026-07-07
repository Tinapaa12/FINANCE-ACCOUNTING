@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div x-data="dashboard()" x-init="initCharts()" class="space-y-6">
    <!-- KPI Cards -->
    <div class="grid grid-cols-4 gap-4">
        <x-kpi-card 
            label="Total Revenue" 
            value="₱1,680,000" 
            change="12.6%" 
            changeType="positive"
            iconBg="bg-green-100"
            iconColor="text-green-600"
            iconPath='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' />

        <x-kpi-card 
            label="Total Expenses" 
            value="₱1,015,000" 
            change="6.3%" 
            changeType="negative"
            iconBg="bg-red-100"
            iconColor="text-red-500"
            iconPath='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>' />

        <x-kpi-card 
            label="Net Profit" 
            value="₱665,000" 
            change="15.4%" 
            changeType="positive"
            iconBg="bg-blue-100"
            iconColor="text-blue-500"
            iconPath='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>' />

        <x-kpi-card 
            label="Cash Balance" 
            value="₱120,000" 
            change="-0.0%" 
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

            <!-- Recent Journal Entry -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Recent Journal Entry</h3>
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
                        @foreach([
                            ['date' => 'June 30, 2024', 'ref' => 'JE - 2024 - 00128', 'desc' => 'Collection from Customer A'],
                            ['date' => 'June 29, 2024', 'ref' => 'JE - 2024 - 00127', 'desc' => 'Payment to Supplier X'],
                            ['date' => 'June 28, 2024', 'ref' => 'JE - 2024 - 00126', 'desc' => 'Purchase of Office Supply'],
                            ['date' => 'June 27, 2024', 'ref' => 'JE - 2024 - 00125', 'desc' => 'Monthly Salary Expense'],
                            ['date' => 'June 26, 2024', 'ref' => 'JE - 2024 - 00124', 'desc' => 'Utility Bill Payment'],
                        ] as $entry)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 text-sm text-gray-600">{{ $entry['date'] }}</td>
                            <td class="py-3 text-sm text-gray-900 font-medium">{{ $entry['ref'] }}</td>
                            <td class="py-3 text-sm text-gray-900">{{ $entry['desc'] }}</td>
                            <td class="py-3"><span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Posted</span></td>
                        </tr>
                        @endforeach
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
                    @foreach([
                        ['label' => 'Chart of Accounts', 'value' => '9'],
                        ['label' => 'Journal Entries', 'value' => '5'],
                        ['label' => 'Active Accounts', 'value' => '9'],
                        ['label' => 'Inactive Accounts', 'value' => '0'],
                    ] as $item)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">{{ $item['label'] }}</span>
                        <span class="font-semibold text-gray-900">{{ $item['value'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Financial Alerts -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Financial Alerts</h3>
                <div class="space-y-2">
                    @foreach([
                        ['color' => 'red', 'text' => '3 Supplier Bills Due Today'],
                        ['color' => 'green', 'text' => '1 Tax Filing Due in June 30'],
                        ['color' => 'yellow', 'text' => '0 Draft Journal Entry'],
                    ] as $alert)
                    <div class="flex items-center gap-2 p-2 bg-{{ $alert['color'] }}-50 rounded-lg border border-{{ $alert['color'] }}-100">
                        <span class="w-2 h-2 rounded-full bg-{{ $alert['color'] }}-500 flex-shrink-0"></span>
                        <span class="text-xs text-{{ $alert['color'] }}-700 font-medium">{{ $alert['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Account Payable Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Account Payable Summary</h3>
                <div class="space-y-3">
                    @foreach([
                        ['bg' => 'yellow', 'label' => 'Paid This Month', 'sub' => '1 Payment', 'value' => '₱54,400'],
                        ['bg' => 'green', 'label' => 'Payments Today', 'sub' => '1 Payments', 'value' => '₱54,400'],
                        ['bg' => 'purple', 'label' => 'Total Bill Pending', 'sub' => '3 Bills', 'value' => '₱58,700'],
                    ] as $card)
                    <div class="p-3 bg-{{ $card['bg'] }}-50 rounded-lg border border-{{ $card['bg'] }}-100">
                        <p class="text-xs text-gray-500 mb-1">{{ $card['label'] }}</p>
                        <p class="text-xs text-gray-500">{{ $card['sub'] }}</p>
                        <p class="text-lg font-bold text-gray-900">{{ $card['value'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Accounts Receivable Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Accounts Receivable Summary</h3>
                <div class="space-y-3">
                    <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                        <p class="text-xs text-green-700 font-medium">Total Collected (This Month)</p>
                        <p class="text-xs text-gray-500">From 3 payments</p>
                        <p class="text-lg font-bold text-gray-900">₱50,500</p>
                    </div>
                    @foreach([
                        ['bg' => 'yellow', 'label' => 'Cleared Payments', 'sub' => 'Fully recorded', 'badge' => '2'],
                        ['bg' => 'red', 'label' => 'Pending Clearance', 'sub' => '₱25,500 — Santos Ent.', 'badge' => '1'],
                    ] as $item)
                    <div class="flex items-center justify-between p-2 bg-{{ $item['bg'] }}-50 rounded-lg border border-{{ $item['bg'] }}-100">
                        <div>
                            <p class="text-xs text-{{ $item['bg'] }}-700 font-medium">{{ $item['label'] }}</p>
                            <p class="text-xs text-gray-500">{{ $item['sub'] }}</p>
                        </div>
                        <span class="w-6 h-6 rounded-full bg-{{ $item['bg'] }}-400 text-white text-xs font-bold flex items-center justify-center">{{ $item['badge'] }}</span>
                    </div>
                    @endforeach
                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <p class="text-xs text-blue-700 font-medium">Top Payment Method</p>
                        <p class="text-xs text-gray-500">Used in 3 of 3 payments</p>
                        <p class="text-sm font-bold text-blue-600">Gcash</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
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
                    
                    cashFlowChartInstance = new Chart(cashCanvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                            datasets: [
                                { label: 'Cash In', data: [120,150,180,140,200,170,190], backgroundColor: '#22c55e', borderRadius: 3, barPercentage: 0.6, categoryPercentage: 0.8 },
                                { label: 'Cash Out', data: [80,90,110,100,130,120,140], backgroundColor: '#ef4444', borderRadius: 3, barPercentage: 0.6, categoryPercentage: 0.8 },
                                { type: 'line', label: 'Net Cash Flow', data: [40,60,70,40,70,50,50], borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', tension: 0.4, pointRadius: 3, pointBackgroundColor: '#3b82f6', borderWidth: 2, fill: true }
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
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                            datasets: [
                                { label: 'Revenue', data: [180,220,280,250,300,270,290], backgroundColor: '#3b82f6', borderRadius: 3, barPercentage: 0.6, categoryPercentage: 0.8 },
                                { label: 'Expenses', data: [120,140,180,160,200,190,210], backgroundColor: '#ef4444', borderRadius: 3, barPercentage: 0.6, categoryPercentage: 0.8 }
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