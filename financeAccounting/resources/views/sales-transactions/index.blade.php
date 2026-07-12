@extends('layouts.app')

@section('title', 'Sales Transactions')
@section('page-title', 'Sales Transactions')

@section('content')
<div x-data="salesApp()" x-init="init()">
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">
            This is a <strong>dummy Sales module</strong> simulating an external ERP system.
            Paid transactions are automatically posted to Finance via <code>FinancePostingService</code>.
        </p>
        <button @click="openCreateModal()" class="flex items-center gap-2 px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Transaction
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Order No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Payment Method</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Finance</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $txn)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $txn->order_no }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $txn->customer_name }}</td>
                    <td class="px-6 py-3 text-sm text-gray-900 font-medium text-right">₱{{ number_format($txn->total_amount, 2) }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $txn->payment_method }}</td>
                    <td class="px-6 py-3">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $txn->status === 'Paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $txn->status }}
                        </span>
                    </td>
                    <td class="px-6 py-3">
                        @if($txn->is_posted_to_finance)
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Posted</span>
                            <span class="text-xs text-gray-400 ml-1">#{{ $txn->journal_entry_id }}</span>
                        @else
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        @if($txn->status === 'Pending')
                            <form action="{{ route('sales-transactions.mark-as-paid', $txn) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs font-medium text-blue-600 hover:text-blue-800">Mark as Paid</button>
                            </form>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">No sales transactions yet. Click "New Transaction" to create one.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-modal show="showCreateModal">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">New Sales Transaction</h3>
            <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form @submit.prevent="submitForm()">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name <span class="text-red-500">*</span></label>
                    <input x-model="form.customer_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="e.g. Juan Cruz" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount (₱) <span class="text-red-500">*</span></label>
                    <input x-model="form.total_amount" type="number" step="0.01" min="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="0.00" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                    <select x-model="form.payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                        <option value="">Select payment method</option>
                        <option value="Cash">Cash</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Installment">Installment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select x-model="form.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">If set to <strong>Paid</strong>, a journal entry will be automatically created in Finance.</p>
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="w-full px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium" x-text="submitting ? 'Creating...' : 'Create Transaction'"></button>
            </div>
        </form>
    </x-modal>

    <x-success-modal show="showSuccessModal" />
</div>
@endsection

@section('scripts')
<script>
    function salesApp() {
        return {
            showCreateModal: false,
            showSuccessModal: false,
            submitting: false,
            form: {
                customer_name: '',
                total_amount: '',
                payment_method: '',
                status: 'Pending',
            },

            init() {},

            openCreateModal() {
                this.form = { customer_name: '', total_amount: '', payment_method: '', status: 'Pending' };
                this.showCreateModal = true;
            },

            async submitForm() {
                this.submitting = true;
                try {
                    const formData = new FormData();
                    formData.append('customer_name', this.form.customer_name);
                    formData.append('total_amount', this.form.total_amount);
                    formData.append('payment_method', this.form.payment_method);
                    formData.append('status', this.form.status);

                    const res = await fetch('{{ route('sales-transactions.store') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData,
                    });

                    if (res.ok) {
                        this.showCreateModal = false;
                        this.showSuccessModal = true;
                        window.location.reload();
                    } else {
                        const data = await res.json();
                        alert(data.message || Object.values(data.errors || {}).flat().join('\n') || 'Create failed');
                    }
                } catch (e) {
                    alert('Network error');
                } finally {
                    this.submitting = false;
                }
            },
        }
    }
</script>
@endsection
