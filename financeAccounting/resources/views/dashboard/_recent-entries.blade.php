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
