@extends('layouts.app')

@section('title', 'Charts of Accounts')
@section('page-title', 'Charts of Accounts')

@section('content')
<div x-data="chartOfAccounts()" x-init="init()">
    <!-- Toolbar -->
    <div class="flex items-center justify-between mb-6">
        <div class="relative w-96">
            <input type="text" x-model="searchQuery" placeholder="Search accounts..." 
                class="w-full pl-4 pr-12 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
            <svg class="w-5 h-5 text-gray-400 absolute right-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <div class="flex items-center gap-3">
            <button @click="exportPDF()" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export
            </button>
            <button @click="openAddModal()" class="flex items-center gap-2 px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                New Account
            </button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <x-filter-tabs active="filter" :options="['All' => 'All Accounts', 'Asset' => 'Assets', 'Liability' => 'Liabilities', 'Equity' => 'Equity', 'Revenue' => 'Revenue', 'Expense' => 'Expenses']" />

    <!-- Accounts Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
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
                                'bg-purple-100 text-purple-700': account.type === 'Expense'
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

    <!-- Account Details -->
    <div x-show="selectedAccount" x-transition class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Account Details</h3>
        <div class="grid grid-cols-2 gap-x-12 gap-y-4 max-w-3xl">
            @foreach([
                ['label' => 'Account Code', 'key' => 'code'],
                ['label' => 'Normal Balance', 'key' => 'normal_balance'],
                ['label' => 'Account Name', 'key' => 'name'],
                ['label' => 'Current Balance', 'key' => 'current_balance', 'currency' => true],
                ['label' => 'Type', 'key' => 'type'],
                ['label' => 'Status', 'key' => 'status'],
                ['label' => 'Date Created', 'key' => 'date_created'],
                ['label' => 'Last Updated', 'key' => 'last_updated'],
            ] as $field)
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 font-medium">{{ $field['label'] }}</span>
                <span class="text-sm text-gray-900">:</span>
                <span class="text-sm text-gray-900 font-medium" 
                      :class="'{{ $field['key'] }}' === 'current_balance' ? (selectedAccount && selectedAccount.current_balance >= 0 ? 'text-green-600' : 'text-red-600') : ''"
                      x-text="selectedAccount ? ('{{ $field['key'] }}' === 'current_balance' ? '₱' + selectedAccount.{{ $field['key'] }}.toLocaleString() : selectedAccount.{{ $field['key'] }}) : ''">
                </span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Add Account Modal -->
    <x-modal show="showAddModal">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900" x-text="editingAccount ? 'Edit Account' : 'Add Account'"></h3>
            <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form @submit.prevent="saveAccount()">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Name <span class="text-red-500">*</span></label>
                    <input x-model="newAccount.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Enter Account Name" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Code <span class="text-red-500">*</span></label>
                        <input x-model="newAccount.code" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Enter Code" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                        <select x-model="newAccount.type" @change="updateNormalBalance()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                            <option value="">Choose type</option>
                            <option value="Asset">Asset</option>
                            <option value="Liability">Liability</option>
                            <option value="Equity">Equity</option>
                            <option value="Revenue">Revenue</option>
                            <option value="Expense">Expense</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Normal Balance <span class="text-red-500">*</span></label>
                        <select x-model="newAccount.normal_balance" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                            <option value="Debit">Debit</option>
                            <option value="Credit">Credit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select x-model="newAccount.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="w-full px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium" x-text="editingAccount ? 'Update Account' : 'Add Account'"></button>
            </div>
        </form>
    </x-modal>

    <!-- Success Modal -->
    <x-success-modal show="showSuccessModal" />

    <!-- PDF Export Modal -->
    <x-success-modal show="showPDFModal" title="PDF EXPORTED!" />
</div>
@endsection

@section('scripts')
<script>
    function chartOfAccounts() {
        return {
            filter: 'All',
            searchQuery: '',
            selectedAccount: null,
            showAddModal: false,
            showSuccessModal: false,
            showPDFModal: false,
            accounts: [],
            editingAccount: null,
            newAccount: { name: '', code: '', type: '', normal_balance: 'Debit', status: 'Active' },

            get filteredAccounts() {
                let result = this.accounts;
                if (this.filter !== 'All') result = result.filter(a => a.type === this.filter);
                if (this.searchQuery) {
                    const q = this.searchQuery.toLowerCase();
                    result = result.filter(a => a.name.toLowerCase().includes(q) || a.code.toLowerCase().includes(q) || a.type.toLowerCase().includes(q));
                }
                return result;
            },

            init() {
                const raw = @json($accounts);
                this.accounts = (raw || []).map(a => ({
                    account_id: a.account_id,
                    code: a.account_code,
                    name: a.account_name,
                    type: a.type,
                    normal_balance: a.normal_balance,
                    status: a.status,
                    current_balance: parseFloat(a.current_balance) || 0,
                    date_created: a.created_at ? new Date(a.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '',
                    last_updated: a.updated_at ? new Date(a.updated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '',
                }));
                if (this.accounts.length > 0) this.selectedAccount = this.accounts[0];
            },

            selectAccount(account) { this.selectedAccount = account; },

            openAddModal() {
                this.editingAccount = null;
                this.newAccount = { name: '', code: '', type: '', normal_balance: 'Debit', status: 'Active' };
                this.showAddModal = true;
            },

            updateNormalBalance() {
                const type = this.newAccount.type;
                if (type === 'Asset' || type === 'Expense') this.newAccount.normal_balance = 'Debit';
                else if (type === 'Liability' || type === 'Equity' || type === 'Revenue') this.newAccount.normal_balance = 'Credit';
            },

            async saveAccount() {
                const isEdit = this.editingAccount;
                const formData = new FormData();
                formData.append('account_name', this.newAccount.name);
                formData.append('type', this.newAccount.type);
                formData.append('normal_balance', this.newAccount.normal_balance);
                formData.append('status', this.newAccount.status);
                if (!isEdit) {
                    formData.append('account_code', this.newAccount.code);
                }
                try {
                    const url = isEdit ? '/chart-of-accounts/' + this.editingAccount.account_id : '{{ route('chart-of-accounts.store') }}';
                    const method = isEdit ? 'POST' : 'POST';
                    if (isEdit) formData.append('_method', 'PUT');
                    const res = await fetch(url, {
                        method: method,
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: formData,
                    });
                    if (res.ok) {
                        this.showAddModal = false;
                        this.showSuccessModal = true;
                        window.location.reload();
                    } else {
                        const data = await res.json();
                        alert(data.message || Object.values(data.errors || {}).flat().join('\n') || 'Save failed');
                    }
                } catch (e) {
                    alert('Network error');
                }
            },

            editAccount(account) {
                this.editingAccount = account;
                this.newAccount = { name: account.name, code: account.code, type: account.type, normal_balance: account.normal_balance, status: account.status };
                this.showAddModal = true;
            },

            async deleteAccount(account) {
                if (!confirm('Delete ' + account.name + '?')) return;
                try {
                    const res = await fetch('/chart-of-accounts/' + account.account_id, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    });
                    if (res.ok) window.location.reload();
                    else alert('Delete failed');
                } catch (e) {
                    alert('Network error');
                }
            },

            exportPDF() { this.showPDFModal = true; }
        }
    }
</script>
@endsection