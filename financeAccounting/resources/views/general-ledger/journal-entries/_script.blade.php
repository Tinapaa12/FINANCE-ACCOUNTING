<script>
function journalEntries() {
    return {
        filter: 'All',
        searchQuery: '',
        selectedEntry: null,
        showAddModal: false,
        showSuccessModal: false,
        entries: [],
        accounts: [],
        editingEntry: false,
        editDate: '',
        editDescription: '',
        editStatus: '',
        editRef: '',
        newJournal: { reference: '', date: '', description: '', status: 'Draft', lines: [] },

        get filteredEntries() {
            let result = this.entries;
            if (this.filter !== 'All') result = result.filter(e => e.status === this.filter);
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                result = result.filter(e => e.reference.toLowerCase().includes(q) || e.description.toLowerCase().includes(q));
            }
            return result;
        },

        get displayLines() {
            return this.selectedEntry ? this.selectedEntry.lines : [];
        },

        init() {
            this.accounts = @json($accounts);
            const raw = @json($entries->items());
            this.entries = (raw || []).map(e => {
                const totalDebit = (e.lines || []).reduce((s, l) => s + parseFloat(l.debit || 0), 0);
                const totalCredit = (e.lines || []).reduce((s, l) => s + parseFloat(l.credit || 0), 0);
                return {
                    journal_entry_id: e.journal_entry_id,
                    transaction_date: e.transaction_date,
                    date: e.transaction_date ? new Date(e.transaction_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '',
                    reference: e.sales_transaction ? e.sales_transaction.order_no : e.reference_no,
                    reference_no: e.reference_no,
                    description: e.description,
                    status: e.status,
                    debit: totalDebit,
                    credit: totalCredit,
                    created_at: e.created_at ? new Date(e.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '',
                    updated_at: e.updated_at ? new Date(e.updated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '',
                    lines: (e.lines || []).map(l => ({
                        account_id: l.account_id,
                        account_code: l.account ? l.account.account_code : '',
                        account_name: l.account ? l.account.account_name : '',
                        description: l.description,
                        debit: parseFloat(l.debit || 0),
                        credit: parseFloat(l.credit || 0),
                    }))
                };
            });
            if (this.entries.length > 0) this.selectedEntry = this.entries[0];
        },

        selectEntry(entry) {
            this.selectedEntry = entry;
            this.editingEntry = false;
        },

        backToTable() {
            this.selectedEntry = null;
            this.editingEntry = false;
        },

        startEdit() {
            this.editDate = this.selectedEntry.transaction_date;
            this.editDescription = this.selectedEntry.description;
            this.editStatus = this.selectedEntry.status;
            this.editRef = this.selectedEntry.reference_no;
            this.editingEntry = true;
        },

        cancelEdit() {
            this.editingEntry = false;
        },

        async saveDetails() {
            const formData = new FormData();
            formData.append('transaction_date', this.editDate);
            formData.append('reference_no', this.editRef);
            formData.append('description', this.editDescription);
            formData.append('status', this.editStatus);
            formData.append('_method', 'PUT');

            try {
                const res = await fetch('/journal-entries/' + this.selectedEntry.journal_entry_id, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formData,
                });
                if (res.ok) {
                    this.editingEntry = false;
                    window.location.reload();
                } else {
                    const data = await res.json();
                    alert(data.message || Object.values(data.errors || {}).flat().join('\n') || 'Save failed');
                }
            } catch (e) {
                alert('Network error');
            }
        },

        get newJournalTotalDebit() {
            return this.newJournal.lines.reduce((s, l) => s + parseFloat(l.debit || 0), 0);
        },
        get newJournalTotalCredit() {
            return this.newJournal.lines.reduce((s, l) => s + parseFloat(l.credit || 0), 0);
        },
        get newJournalBalanced() {
            return this.newJournalTotalDebit === this.newJournalTotalCredit;
        },

        openAddModal() {
            this.newJournal = {
                reference: '', date: new Date().toISOString().split('T')[0], description: '', status: 'Draft',
                lines: [
                    { account_id: '', description: '', debit: 0, credit: 0 },
                    { account_id: '', description: '', debit: 0, credit: 0 },
                ]
            };
            this.showAddModal = true;
        },

        addNewLine() {
            this.newJournal.lines.push({ account_id: '', description: '', debit: 0, credit: 0 });
        },

        removeNewLine(index) {
            if (this.newJournal.lines.length <= 2) return;
            this.newJournal.lines.splice(index, 1);
        },

        async saveJournal() {
            const formData = new FormData();
            formData.append('transaction_date', this.newJournal.date);
            formData.append('reference_no', this.newJournal.reference);
            formData.append('description', this.newJournal.description);
            formData.append('status', this.newJournal.status);

            const validLines = this.newJournal.lines.filter(l => l.account_id);
            validLines.forEach((line, i) => {
                formData.append('lines[' + i + '][account_id]', line.account_id);
                formData.append('lines[' + i + '][description]', line.description);
                formData.append('lines[' + i + '][debit]', line.debit || 0);
                formData.append('lines[' + i + '][credit]', line.credit || 0);
            });

            try {
                const res = await fetch('{{ route('journal-entries.store') }}', {
                    method: 'POST',
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

        async deleteEntry(entry) {
            if (!confirm('Delete ' + entry.reference + '?')) return;
            try {
                const res = await fetch('/journal-entries/' + entry.journal_entry_id, {
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
