@extends('layouts.app')

@section('page-title', 'Inventory Tracking')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Receive Goods into Inventory</h2>
            <p class="text-sm text-gray-500 mt-1">Record items received from suppliers. This will create a Goods Received Note and update inventory stock.</p>
        </div>

        @if(session('success'))
        <div class="mx-6 mt-4 px-4 py-3 bg-green-100 text-green-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('inventory.tracking.receive') }}" class="p-6">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Order</label>
                <select name="purchase_order_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— No PO (Direct Receiving) —</option>
                    @foreach($purchaseOrders as $po)
                    <option value="{{ $po->id }}">{{ $po->po_no }} - {{ $po->supplier }} ({{ $po->item_name ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Item <span class="text-red-500">*</span></label>
                <select name="inventory_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select Item —</option>
                    @foreach($items as $item)
                    <option value="{{ $item->id }}" data-price="{{ $item->price }}">
                        {{ $item->item_name }} (Stock: {{ $item->quantity }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Received <span class="text-red-500">*</span></label>
                <input type="number" name="qty_received" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                <input type="text" name="supplier" placeholder="e.g. Acme Corp" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference (DR / Invoice #)</label>
                <input type="text" name="reference" placeholder="e.g. DR-12345" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('inventory.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors no-underline">Cancel</a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">Record Received</button>
            </div>
        </form>
    </div>

    <div class="mt-4 text-xs text-gray-400 text-center">
        This will create a GRN (Pending) and Supplier Bill (Pending) in Accounts Payable.
    </div>
</div>
@endsection