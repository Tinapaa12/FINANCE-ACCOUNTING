<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
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
