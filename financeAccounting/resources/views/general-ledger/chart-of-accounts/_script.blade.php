<script>
function chartOfAccounts() {
    return {
        filter: 'All',
        searchQuery: '',
        selectedAccount: null,
        showAddModal: false,
        showSuccessModal: false,
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
            const raw = @json($accounts->items());
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

    }
}
</script>
