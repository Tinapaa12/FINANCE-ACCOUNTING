<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-y-auto" style="max-height: 568px;">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Code</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Account Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Normal Balance</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Current Balance</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <template x-for="account in filteredAccounts" :key="account.code">
                <tr @click="selectAccount(account)" :class="selectedAccount && selectedAccount.code === account.code ? 'bg-blue-50' : 'hover:bg-gray-50'" class="cursor-pointer transition-colors">
                    <td class="px-6 py-3 text-sm text-gray-900 font-medium" x-text="account.code"></td>
                    <td class="px-6 py-3 text-sm text-gray-900" x-text="account.name"></td>
                    <td class="px-6 py-3">
                        <span :class="{
                            'bg-blue-100 text-blue-700': account.type === 'Asset',
                            'bg-orange-100 text-orange-700': account.type === 'Liability',
                            'bg-green-100 text-green-700': account.type === 'Revenue',
                            'bg-purple-100 text-purple-700': account.type === 'Expense',
                            'bg-pink-100 text-pink-700': account.type === 'Equity'
                        }" class="px-3 py-1 rounded-full text-xs font-medium" x-text="account.type"></span>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-600" x-text="account.normal_balance"></td>
                    <td class="px-6 py-3 text-sm font-medium" :class="account.current_balance >= 0 ? 'text-green-600' : 'text-red-600'" x-text="'₱' + account.current_balance.toLocaleString()"></td>
                    <td class="px-6 py-3">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700" x-text="account.status"></span>
                    </td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            <button @click.stop="editAccount(account)" class="text-amber-500 hover:text-amber-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button @click.stop="deleteAccount(account)" class="text-red-500 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            </template>
            <tr x-show="filteredAccounts.length === 0">
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No accounts found.</td>
            </tr>
        </tbody>
    </table>
    </div>
</div>

<div class="mt-4 mb-6">
    {{ $accounts->links('pagination::tailwind') }}
</div>
