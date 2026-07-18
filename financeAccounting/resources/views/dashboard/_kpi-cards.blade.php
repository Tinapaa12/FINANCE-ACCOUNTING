<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
