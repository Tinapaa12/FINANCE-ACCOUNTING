<div class="lg:col-span-3 space-y-4">
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
