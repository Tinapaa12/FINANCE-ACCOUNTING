@extends('layouts.app')

@section('title', 'Journal Entry')
@section('page-title', 'Journal Entry')

@section('content')
<div x-data="journalEntries()" x-init="init()">
    <!-- Toolbar -->
    <div class="flex items-center justify-between mb-6">
        <div class="relative w-96">
            <input type="text" x-model="searchQuery" placeholder="Search entries..." 
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
                New Journal
            </button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <x-filter-tabs active="filter" :options="['All' => 'All', 'Draft' => 'Drafts', 'Posted' => 'Posted']" />

    <!-- Entries Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Debit</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Credit</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-for="entry in filteredEntries" :key="entry.id">
                    <tr @click="selectEntry(entry)" :class="selectedEntry && selectedEntry.id === entry.id ? 'bg-blue-50' : 'hover:bg-gray-50'" class="cursor-pointer transition-colors">
                        <td class="px-6 py-3 text-sm text-gray-600" x-text="entry.date"></td>
                        <td class="px-6 py-3 text-sm text-gray-900 font-medium" x-text="entry.reference"></td>
                        <td class="px-6 py-3 text-sm text-gray-900" x-text="entry.description"></td>
                        <td class="px-6 py-3">
                            <span :class="entry.status === 'Posted' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'" class="px-3 py-1 rounded-full text-xs font-medium" x-text="entry.status"></span>
                        </td>
                        <td class="px-6 py-3 text-sm font-medium text-gray-900" x-text="'₱' + entry.debit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></td>
                        <td class="px-6 py-3 text-sm font-medium text-gray-900" x-text="'₱' + entry.credit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <button @click.stop="editEntry(entry)" class="text-amber-500 hover:text-amber-600">
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

    <!-- Entry Details -->
    <div x-show="selectedEntry" x-transition class="grid grid-cols-12 gap-6">
        <!-- Journal Entry Details -->
        <div class="col-span-3 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4">Journal Entry Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Reference No.</label>
                    <input x-model="selectedEntry.reference" type="text" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm" readonly>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
                    <div class="relative">
                        <input x-model="selectedEntry.date" type="text" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm" readonly>
                        <svg class="w-4 h-4 text-gray-400 absolute right-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
                    <textarea x-model="selectedEntry.description" rows="3" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm resize-none" readonly></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <input x-model="selectedEntry.status" type="text" :class="selectedEntry.status === 'Posted' ? 'text-green-600 bg-green-50' : 'text-gray-600 bg-gray-50'" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm font-medium" readonly>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Attachments</label>
                    <button class="flex items-center gap-2 w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Upload File
                    </button>
                </div>
            </div>
        </div>

        <!-- Journal Entry Lines -->
        <div class="col-span-6 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4">Journal Entry Lines</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase pb-2">Account Code</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase pb-2">Account Name</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase pb-2">Description</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase pb-2">Debit</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase pb-2">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(line, index) in selectedEntryLines" :key="index">
                            <tr class="border-b border-gray-100">
                                <td class="py-2 text-sm text-gray-900" x-text="line.account_code"></td>
                                <td class="py-2 text-sm text-gray-900" x-text="line.account_name"></td>
                                <td class="py-2 text-sm text-gray-600" x-text="line.description"></td>
                                <td class="py-2 text-sm text-gray-900 text-right" x-text="line.debit > 0 ? line.debit.toLocaleString(undefined, {minimumFractionDigits: 2}) : '0.00'"></td>
                                <td class="py-2 text-sm text-gray-900 text-right" x-text="line.credit > 0 ? line.credit.toLocaleString(undefined, {minimumFractionDigits: 2}) : '0.00'"></td>
                            </tr>
                        </template>
                        <tr>
                            <td class="py-2"><input type="text" placeholder="Enter Code" class="w-full px-2 py-1 text-sm border border-gray-200 rounded"></td>
                            <td class="py-2"><input type="text" placeholder="Enter Account" class="w-full px-2 py-1 text-sm border border-gray-200 rounded"></td>
                            <td class="py-2"><input type="text" placeholder="Enter Description" class="w-full px-2 py-1 text-sm border border-gray-200 rounded"></td>
                            <td class="py-2"><input type="text" value="0.00" class="w-full px-2 py-1 text-sm border border-gray-200 rounded text-right"></td>
                            <td class="py-2"><input type="text" value="0.00" class="w-full px-2 py-1 text-sm border border-gray-200 rounded text-right"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button class="w-full mt-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Line
            </button>
            <div class="flex justify-end gap-3 mt-4">
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium text-sm">Back</button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium text-sm">Edit</button>
                <button class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 font-medium text-sm">Cancel</button>
                <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium text-sm">Save</button>
            </div>
        </div>

        <!-- Entry Summary -->
        <div class="col-span-3 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4">Entry Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Reference no.</span>
                    <span class="font-medium text-gray-900" x-text="selectedEntry ? selectedEntry.reference : ''"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Status</span>
                    <span :class="selectedEntry && selectedEntry.status === 'Posted' ? 'text-green-600' : 'text-gray-600'" class="font-medium" x-text="selectedEntry ? selectedEntry.status : ''"></span>
                </div>
                <div class="border-t border-gray-200 pt-3 mt-3">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Total Debit</span>
                        <span class="font-bold text-gray-900" x-text="selectedEntry ? '₱' + selectedEntry.debit.toLocaleString(undefined, {minimumFractionDigits: 2}) : ''"></span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Total Credit</span>
                        <span class="font-bold text-gray-900" x-text="selectedEntry ? '₱' + selectedEntry.credit.toLocaleString(undefined, {minimumFractionDigits: 2}) : ''"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Difference</span>
                        <span class="font-bold text-green-600">₱0.00</span>
                    </div>
                    <div class="mt-3">
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Balanced</span>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-3 mt-3 text-xs text-gray-500 space-y-1">
                    <div class="flex justify-between">
                        <span>Created at</span>
                        <span x-text="selectedEntry ? selectedEntry.created_at : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Last Updated</span>
                        <span x-text="selectedEntry ? selectedEntry.created_at : ''"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Journal Modal -->
    <x-modal show="showAddModal">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">Add Journal</h3>
            <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form @submit.prevent="saveJournal()">
            <div class="space-y-4">
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
                    <textarea x-model="newJournal.description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none" placeholder="e.g. Collection from Customer Z" required></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select x-model="newJournal.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                        <option value="Active">Active</option>
                        <option value="Draft">Draft</option>
                        <option value="Posted">Posted</option>
                    </select>
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="w-full px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium">Add Journal</button>
            </div>
        </form>
    </x-modal>

    <!-- Success Modal -->
    <x-success-modal show="showSuccessModal" />
</div>
@endsection

@section('scripts')
<script>
    function journalEntries() {
        return {
            filter: 'All',
            searchQuery: '',
            selectedEntry: null,
            showAddModal: false,
            showSuccessModal: false,
            entries: @json($entries),
            newJournal: { reference: '', date: '', description: '', status: 'Active' },
            
            get filteredEntries() {
                let result = this.entries;
                if (this.filter !== 'All') result = result.filter(e => e.status === this.filter);
                if (this.searchQuery) {
                    const q = this.searchQuery.toLowerCase();
                    result = result.filter(e => e.reference.toLowerCase().includes(q) || e.description.toLowerCase().includes(q));
                }
                return result;
            },
            
            get selectedEntryLines() {
                return this.selectedEntry ? this.selectedEntry.lines : [];
            },
            
            init() { if (this.entries.length > 0) this.selectedEntry = this.entries[0]; },
            selectEntry(entry) { this.selectedEntry = entry; },
            openAddModal() { this.newJournal = { reference: '', date: new Date().toISOString().split('T')[0], description: '', status: 'Active' }; this.showAddModal = true; },
            
            saveJournal() {
                const entry = {
                    id: Date.now(),
                    ...this.newJournal,
                    date: new Date(this.newJournal.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }),
                    debit: 0,
                    credit: 0,
                    created_at: new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }),
                    lines: []
                };
                this.entries.unshift(entry);
                this.showAddModal = false;
                this.showSuccessModal = true;
                this.selectedEntry = entry;
            },
            
            editEntry(entry) { alert('Edit functionality - ' + entry.reference); },
            
            deleteEntry(entry) {
                if (confirm('Are you sure you want to delete ' + entry.reference + '?')) {
                    this.entries = this.entries.filter(e => e.id !== entry.id);
                    if (this.selectedEntry && this.selectedEntry.id === entry.id) this.selectedEntry = this.entries.length > 0 ? this.entries[0] : null;
                }
            },
            
            exportPDF() { alert('PDF Export functionality'); }
        }
    }
</script>
@endsection