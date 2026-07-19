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
