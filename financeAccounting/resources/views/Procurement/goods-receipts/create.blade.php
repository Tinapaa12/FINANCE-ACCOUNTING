@extends('layouts.app')

@section('page-title', 'Receive Goods')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Receive Goods</h2>
            <p class="text-sm text-gray-500 mt-1">Record receipt of goods against a confirmed purchase order.</p>
        </div>

        @if(session('success'))
        <div class="mx-6 mt-4 px-4 py-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('procurement.gr.store') }}" class="p-6">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Order *</label>
                <select name="purchase_order_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select PO —</option>
                    @foreach($purchaseOrders as $po)
                    <option value="{{ $po->id }}" {{ request('po_id') == $po->id ? 'selected' : '' }}>
                        {{ $po->po_no }} - {{ $po->supplier }} ({{ $po->item_name ?? 'N/A' }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Received *</label>
                <input type="number" name="qty_received" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Received Date</label>
                <input type="date" name="received_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('procurement.gr.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors no-underline">Cancel</a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">Record Receipt</button>
            </div>
        </form>
    </div>
</div>
@endsection
