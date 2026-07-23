<div class="lg:col-span-3 space-y-4">

    {{-- Accounts Payable Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Accounts Payable
        </h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Outstanding Balance</span>
                <span class="font-semibold text-gray-900">₱{{ number_format($apSummary['outstanding'], 2) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Total Bills</span>
                <span class="font-semibold text-gray-900">{{ $apSummary['total_bills'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Pending</span>
                <span class="font-semibold text-gray-900">{{ $apSummary['pending_bills'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Approved</span>
                <span class="font-semibold text-gray-900">{{ $apSummary['approved_bills'] }}</span>
            </div>
            @if($apSummary['overdue_bills'] > 0)
            <div class="flex justify-between items-center text-red-600">
                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Overdue</span>
                <span class="font-semibold">{{ $apSummary['overdue_bills'] }} (₱{{ number_format($apSummary['overdue_amount'], 2) }})</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Accounts Receivable Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Accounts Receivable
        </h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Outstanding Balance</span>
                <span class="font-semibold text-gray-900">₱{{ number_format($arSummary['outstanding'], 2) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Total Transactions</span>
                <span class="font-semibold text-gray-900">{{ $arSummary['total_transactions'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Pending</span>
                <span class="font-semibold text-gray-900">{{ $arSummary['pending_transactions'] }}</span>
            </div>
            @if($arSummary['overdue_invoices'] > 0)
            <div class="flex justify-between items-center text-red-600">
                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Overdue</span>
                <span class="font-semibold">{{ $arSummary['overdue_invoices'] }} (₱{{ number_format($arSummary['overdue_amount'], 2) }})</span>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-3">Accounts Summary</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Chart of Accounts</span>
                <span class="font-semibold text-gray-900">{{ $accountsSummary['total_accounts'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Journal Entries</span>
                <span class="font-semibold text-gray-900">{{ \App\Models\GeneralLedger\JournalEntry::count() }}</span>
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

</div>
