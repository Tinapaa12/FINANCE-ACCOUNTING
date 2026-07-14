@extends('layouts.app')

@section('page-title', 'Purchase Orders')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold">Purchase Orders</h2>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors" onclick="openPOModal()">Add PO</button>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">PO No.</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Expected Delivery</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
@forelse($purchaseOrders as $po)
<tr class="hover:bg-gray-50 transition-colors">
    <td class="px-4 py-4 border-t border-gray-100 text-sm font-medium">{{ $po->po_no }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $po->supplier }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">₱{{ number_format($po->amount, 2) }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $po->order_date->format('M d, Y') }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $po->expected_delivery ? $po->expected_delivery->format('M d, Y') : 'N/A' }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">
        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
            @if(strtolower($po->status) === 'approved') bg-yellow-100 text-yellow-800
            @elseif(strtolower($po->status) === 'received') bg-green-100 text-green-800
            @elseif(strtolower($po->status) === 'cancelled') bg-red-100 text-red-800
            @else bg-purple-100 text-purple-800 @endif">
            {{ $po->status }}
        </span>
    </td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">
        <div class="flex gap-2 items-center">
            <button class="border-none cursor-pointer rounded-md bg-orange-500 text-white w-8 h-8 flex items-center justify-center hover:bg-orange-600 transition-colors"
                onclick="editPO({{ $po->id }}, '{{ $po->supplier }}', {{ $po->amount }}, '{{ $po->order_date->format('Y-m-d') }}', '{{ $po->expected_delivery ? $po->expected_delivery->format('Y-m-d') : '' }}', '{{ $po->status }}', '{{ addslashes($po->description) }}')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
            </button>
            <form action="{{ route('purchase-orders.destroy', $po->id) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="border-none cursor-pointer rounded-md bg-red-500 text-white w-8 h-8 flex items-center justify-center hover:bg-red-600 transition-colors"
                    onclick="return confirm('Delete this PO?')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center px-4 py-8 text-gray-500">No purchase orders found.</td>
</tr>
@endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $purchaseOrders->links() }}
        </div>
    </div>

</div>

<!-- Add PO Modal -->
<div id="poModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
    <div class="w-[480px] bg-white rounded-xl shadow-xl p-8">
        <h2 class="text-xl font-bold mb-6">Add Purchase Order</h2>
        <form method="POST" action="{{ route('purchase-orders.store') }}">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4 col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier :</label>
                    <input type="text" name="supplier" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount :</label>
                    <input type="number" name="amount" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Date :</label>
                    <input type="date" name="order_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery :</label>
                    <input type="date" name="expected_delivery" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status :</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Received">Received</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="mb-4 col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description :</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closePOModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Add PO</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit PO Modal -->
<div id="editPOModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
    <div class="w-[480px] bg-white rounded-xl shadow-xl p-8">
        <h2 class="text-xl font-bold mb-6">Edit Purchase Order</h2>
        <form id="editPOForm" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4 col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier :</label>
                    <input type="text" name="supplier" id="editPOSupplier" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount :</label>
                    <input type="number" name="amount" id="editPOAmount" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Date :</label>
                    <input type="date" name="order_date" id="editPOOrderDate" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery :</label>
                    <input type="date" name="expected_delivery" id="editPOExpectedDelivery" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status :</label>
                    <select name="status" id="editPOStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Received">Received</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="mb-4 col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description :</label>
                    <textarea name="description" id="editPODescription" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeEditPOModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPOModal() { document.getElementById('poModal').classList.add('active'); }
function closePOModal() { document.getElementById('poModal').classList.remove('active'); }
document.getElementById('poModal')?.addEventListener('click', function(e) { if (e.target === this) closePOModal(); });

function editPO(id, supplier, amount, orderDate, expectedDelivery, status, description) {
    document.getElementById('editPOSupplier').value = supplier;
    document.getElementById('editPOAmount').value = amount;
    document.getElementById('editPOOrderDate').value = orderDate;
    document.getElementById('editPOExpectedDelivery').value = expectedDelivery;
    document.getElementById('editPOStatus').value = status;
    document.getElementById('editPODescription').value = description;
    document.getElementById('editPOForm').action = '/purchase-orders/' + id;
    document.getElementById('editPOModal').classList.add('active');
}
function closeEditPOModal() { document.getElementById('editPOModal').classList.remove('active'); }
document.getElementById('editPOModal')?.addEventListener('click', function(e) { if (e.target === this) closeEditPOModal(); });
</script>

@endsection
