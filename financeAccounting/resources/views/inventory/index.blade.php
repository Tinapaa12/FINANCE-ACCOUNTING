@extends('layouts.app')

@section('page-title', 'Inventory')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold">Inventory Items</h2>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors" onclick="openAddItemModal()">Add Item</button>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Expiry Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
@forelse($items as $item)
<tr class="hover:bg-gray-50 transition-colors">
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $item->id }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm font-medium">{{ $item->item_name }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $item->quantity }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">₱{{ number_format($item->price, 2) }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">
        @if($item->expiration_date)
            <span class="{{ $item->expiration_date->isPast() ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                {{ $item->expiration_date->format('M d, Y') }}
                @if($item->expiration_date->isPast())
                    (Expired)
                @endif
            </span>
        @else
            <span class="text-gray-400">—</span>
        @endif
    </td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">
        <div class="flex gap-1.5 items-center flex-wrap">
            <button onclick="openStockInModal({{ $item->id }}, '{{ addslashes($item->item_name) }}')"
                class="border-none cursor-pointer rounded-md bg-blue-500 text-white px-2 py-1 text-xs font-medium hover:bg-blue-600 transition-colors">Stock In</button>
            <button onclick="openStockOutModal({{ $item->id }}, '{{ addslashes($item->item_name) }}', {{ $item->quantity }})"
                class="border-none cursor-pointer rounded-md bg-orange-500 text-white px-2 py-1 text-xs font-medium hover:bg-orange-600 transition-colors">Stock Out</button>
            <a href="{{ route('inventory.purchase', $item->id) }}"
               class="inline-flex px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-medium hover:bg-green-700 transition-colors no-underline"
               onclick="return confirm('Purchase this item?')">
                Purchase
            </a>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center px-4 py-8 text-gray-500">No inventory items found.</td>
</tr>
@endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $items->links() }}
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold">Transaction History</h2>
    </div>
    <div class="w-full overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Unit Price</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reference</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Notes</th>
                </tr>
            </thead>
            <tbody>
@forelse($transactions as $txn)
<tr class="hover:bg-gray-50 transition-colors">
    <td class="px-4 py-4 border-t border-gray-100 text-sm whitespace-nowrap">{{ $txn->transaction_date->format('M d, Y h:i A') }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $txn->inventory->item_name ?? 'Deleted' }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">
        <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold
            @if($txn->type === 'stock_in') bg-green-100 text-green-800
            @else bg-red-100 text-red-800 @endif">
            {{ $txn->type === 'stock_in' ? 'IN' : 'OUT' }}
        </span>
    </td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $txn->qty }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">₱{{ number_format($txn->unit_price ?? 0, 2) }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $txn->reference ?? '—' }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm max-w-[200px] truncate">{{ $txn->notes ?? '—' }}</td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center px-4 py-8 text-gray-500">No transactions yet.</td>
</tr>
@endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $transactions->links() }}
    </div>
</div>

<!-- Add Item Modal -->
<div id="addItemModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
    <div class="w-[420px] bg-white rounded-xl shadow-xl p-8">
        <h2 class="text-xl font-bold mb-6">Add Inventory Item</h2>
        <form method="POST" action="{{ route('inventory.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Item Name :</label>
                <input type="text" name="item_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity :</label>
                    <input type="number" name="quantity" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price :</label>
                    <input type="number" name="price" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Expiration Date :</label>
                <input type="date" name="expiration_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeAddItemModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Add Item</button>
            </div>
        </form>
    </div>
</div>

<!-- Stock In Modal -->
<div id="stockInModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
    <div class="w-[420px] bg-white rounded-xl shadow-xl p-8">
        <h2 class="text-xl font-bold mb-6">Stock In - <span id="stockInItemName"></span></h2>
        <form method="POST" action="{{ route('inventory.stock-in') }}">
            @csrf
            <input type="hidden" name="inventory_id" id="stockInItemId">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity :</label>
                <input type="number" name="qty" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price :</label>
                <input type="number" name="unit_price" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference :</label>
                <input type="text" name="reference" placeholder="e.g. Supplier delivery, Return" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes :</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeStockInModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Record Stock In</button>
            </div>
        </form>
    </div>
</div>

<!-- Stock Out Modal -->
<div id="stockOutModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
    <div class="w-[420px] bg-white rounded-xl shadow-xl p-8">
        <h2 class="text-xl font-bold mb-6">Stock Out - <span id="stockOutItemName"></span></h2>
        <form method="POST" action="{{ route('inventory.stock-out') }}">
            @csrf
            <input type="hidden" name="inventory_id" id="stockOutItemId">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity :</label>
                <input type="number" name="qty" id="stockOutQty" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1" id="stockOutAvailable">Available: 0</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference :</label>
                <input type="text" name="reference" placeholder="e.g. Sales, Production, Transfer" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes :</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeStockOutModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Record Stock Out</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddItemModal() { document.getElementById('addItemModal').classList.add('active'); }
function closeAddItemModal() { document.getElementById('addItemModal').classList.remove('active'); }
document.getElementById('addItemModal')?.addEventListener('click', function(e) { if (e.target === this) closeAddItemModal(); });

function openStockInModal(id, name) {
    document.getElementById('stockInItemId').value = id;
    document.getElementById('stockInItemName').textContent = name;
    document.getElementById('stockInModal').classList.add('active');
}
function closeStockInModal() { document.getElementById('stockInModal').classList.remove('active'); }
document.getElementById('stockInModal')?.addEventListener('click', function(e) { if (e.target === this) closeStockInModal(); });

function openStockOutModal(id, name, available) {
    document.getElementById('stockOutItemId').value = id;
    document.getElementById('stockOutItemName').textContent = name;
    document.getElementById('stockOutQty').max = available;
    document.getElementById('stockOutAvailable').textContent = 'Available: ' + available;
    document.getElementById('stockOutModal').classList.add('active');
}
function closeStockOutModal() { document.getElementById('stockOutModal').classList.remove('active'); }
document.getElementById('stockOutModal')?.addEventListener('click', function(e) { if (e.target === this) closeStockOutModal(); });
</script>
@endsection