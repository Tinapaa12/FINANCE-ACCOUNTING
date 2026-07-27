@extends('layouts.app')

@section('title', 'Sales & Customer Support Management Refund')
@section('page-title', 'Sales & Customer Support Management Refund')

@section('content')
<div class="max-w-2xl mx-auto">
    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-sm mb-6">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">New Refund Request</h3>
            <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium">ERP Simulation</span>
        </div>

        <form method="POST" action="{{ route('sales-refund.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Order Number <span class="text-red-500">*</span></label>
                <select id="transaction_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="">Select a paid transaction</option>
                    @foreach($transactions as $t)
                        <option value="{{ $t->sales_transaction_id }}" data-order="{{ $t->order_no }}" data-customer="{{ $t->customer_name }}" data-amount="{{ $t->total_amount }}" data-payment="{{ $t->payment_method }}">
                            {{ $t->order_no }} – {{ $t->customer_name }} (₱{{ number_format($t->total_amount, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" name="sales_transaction_id" id="sales_transaction_id">
            <input type="hidden" name="refund_request_id" id="refund_request_id">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                <div id="customer_name_display" class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500">—</div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Refund Amount (₱) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="refund_amount" id="refund_amount" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Refund Status <span class="text-red-500">*</span></label>
                <select name="refund_status_display" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" disabled>
                    <option value="Approved" selected>Approved</option>
                </select>
                <input type="hidden" name="refund_status" value="Approved">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Refund Method <span class="text-red-500">*</span></label>
                <select name="refund_method" id="refund_method" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="">Select method</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="GCash">GCash</option>
                    <option value="PayPal">PayPal</option>
                    <option value="Cash">Cash</option>
                </select>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium flex items-center gap-2">
                    Submit Refund
                </button>
                <a href="{{ route('sales-refund.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('transaction_select').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (!this.value) {
        document.getElementById('sales_transaction_id').value = '';
        document.getElementById('refund_request_id').value = '';
        document.getElementById('refund_amount').value = '';
        document.getElementById('customer_name_display').textContent = '—';
        document.getElementById('customer_name_display').className = 'w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500';
        document.getElementById('refund_method').value = '';
        return;
    }
    document.getElementById('sales_transaction_id').value = this.value;
    document.getElementById('refund_request_id').value = 'REF-' + opt.dataset.order;
    document.getElementById('refund_amount').value = opt.dataset.amount;
    document.getElementById('customer_name_display').textContent = opt.dataset.customer;
    document.getElementById('customer_name_display').className = 'w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-900 font-medium';

    const methodSelect = document.getElementById('refund_method');
    let matched = false;
    for (const mOpt of methodSelect.options) {
        if (mOpt.value === opt.dataset.payment) {
            mOpt.selected = true;
            matched = true;
            break;
        }
    }
    if (!matched) methodSelect.value = '';
});
</script>
@endpush
@endsection
