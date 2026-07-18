<x-modal show="showAddModal">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-gray-900">Add Journal</h3>
        <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    <form @submit.prevent="saveJournal()" class="max-h-[80vh] overflow-y-auto">
        <div class="space-y-4 pr-2">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reference <span class="text-red-500">*</span></label>
                    <input x-model="newJournal.reference" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Enter Reference" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                    <input x-model="newJournal.date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                <textarea x-model="newJournal.description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none" placeholder="e.g. Collection from Customer Z" required></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select x-model="newJournal.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                    <option value="Draft">Draft</option>
                    <option value="Posted">Posted</option>
                </select>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">Journal Lines</label>
                    <button @click.prevent="addNewLine()" type="button" class="text-xs text-blue-600 hover:text-blue-800 font-medium">+ Add Line</button>
                </div>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-1.5 text-left text-xs font-semibold text-gray-500">Account</th>
                                <th class="px-2 py-1.5 text-left text-xs font-semibold text-gray-500">Description</th>
                                <th class="px-2 py-1.5 text-right text-xs font-semibold text-gray-500">Debit</th>
                                <th class="px-2 py-1.5 text-right text-xs font-semibold text-gray-500">Credit</th>
                                <th class="px-2 py-1.5 text-center text-xs font-semibold text-gray-500"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(line, i) in newJournal.lines" :key="i">
                                <tr>
                                    <td class="px-2 py-1">
                                        <select x-model="line.account_id" class="w-full px-1.5 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                            <option value="">Select</option>
                                            <template x-for="acct in accounts" :key="acct.account_id">
                                                <option :value="acct.account_id" x-text="acct.account_code + ' - ' + acct.account_name"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="px-2 py-1">
                                        <input x-model="line.description" type="text" class="w-full px-1.5 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Desc">
                                    </td>
                                    <td class="px-2 py-1">
                                        <input x-model="line.debit" type="number" step="0.01" min="0" class="w-full px-1.5 py-1 text-xs border border-gray-300 rounded text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                    </td>
                                    <td class="px-2 py-1">
                                        <input x-model="line.credit" type="number" step="0.01" min="0" class="w-full px-1.5 py-1 text-xs border border-gray-300 rounded text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                    </td>
                                    <td class="px-2 py-1 text-center">
                                        <button @click.prevent="removeNewLine(i)" type="button" class="text-red-400 hover:text-red-600" x-show="newJournal.lines.length > 2">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200">
                            <tr>
                                <td colspan="2" class="px-2 py-1.5 text-xs font-semibold text-gray-600">Totals</td>
                                <td class="px-2 py-1.5 text-xs font-semibold text-right" x-text="'₱' + newJournalTotalDebit.toFixed(2)"></td>
                                <td class="px-2 py-1.5 text-xs font-semibold text-right" x-text="'₱' + newJournalTotalCredit.toFixed(2)"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-2 py-1 text-xs">
                                    <span :class="newJournalBalanced ? 'text-green-600' : 'text-red-600'" x-text="newJournalBalanced ? 'Balanced' : 'Unbalanced'"></span>
                                    <span x-show="!newJournalBalanced" class="text-gray-400"> — diff: ₱<span x-text="Math.abs(newJournalTotalDebit - newJournalTotalCredit).toFixed(2)"></span></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="w-full px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium">Add Journal</button>
        </div>
    </form>
</x-modal>
