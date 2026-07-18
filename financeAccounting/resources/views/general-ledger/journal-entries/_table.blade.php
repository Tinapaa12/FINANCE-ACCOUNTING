<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto overflow-y-auto" style="max-height: 568px;">
    <table class="w-full min-w-[700px]">
        <thead class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10">
            <tr>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reference</th>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Debit</th>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Credit</th>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <template x-for="entry in filteredEntries" :key="entry.journal_entry_id">
                <tr @click="selectEntry(entry)" :class="selectedEntry && selectedEntry.journal_entry_id === entry.journal_entry_id ? 'bg-blue-50' : 'hover:bg-gray-50'" class="cursor-pointer transition-colors">
                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-600 whitespace-nowrap" x-text="entry.date"></td>
                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-900 font-medium whitespace-nowrap" x-text="entry.reference"></td>
                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-900 truncate max-w-[200px]" x-text="entry.description"></td>
                    <td class="px-4 sm:px-6 py-3 whitespace-nowrap">
                        <span :class="entry.status === 'Posted' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'" class="px-3 py-1 rounded-full text-xs font-medium" x-text="entry.status"></span>
                    </td>
                    <td class="px-4 sm:px-6 py-3 text-sm font-medium text-gray-900 whitespace-nowrap" x-text="'₱' + entry.debit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></td>
                    <td class="px-4 sm:px-6 py-3 text-sm font-medium text-gray-900 whitespace-nowrap" x-text="'₱' + entry.credit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></td>
                    <td class="px-4 sm:px-6 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <button @click.stop="selectEntry(entry); startEdit()" class="text-amber-500 hover:text-amber-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button @click.stop="deleteEntry(entry)" class="text-red-500 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            </template>
            <tr x-show="filteredEntries.length === 0">
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No journal entries found.</td>
            </tr>
        </tbody>
    </table>
    </div>
</div>

<div class="mt-4 mb-6">
    {{ $entries->links('pagination::tailwind') }}
</div>
