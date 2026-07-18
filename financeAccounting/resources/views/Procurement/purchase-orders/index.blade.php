@extends('layouts.app')

@section('page-title', 'Purchase Orders')

@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold">All Purchase Orders</h2>
        <button type="button" onclick="openModal('createPOModal')" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">+ New PO</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-6 py-3 font-medium text-gray-600">PO #</th>
                    <th class="px-6 py-3 font-medium text-gray-600">Supplier</th>
                    <th class="px-6 py-3 font-medium text-gray-600">Item</th>
                    <th class="px-6 py-3 font-medium text-gray-600">Qty</th>
                    <th class="px-6 py-3 font-medium text-gray-600">Amount</th>
                    <th class="px-6 py-3 font-medium text-gray-600">Status</th>
                    <th class="px-6 py-3 font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($purchaseOrders as $po)
                <tr>
                    <td class="px-6 py-4 font-mono text-xs">{{ $po->po_no }}</td>
                    <td class="px-6 py-4 max-w-[160px] truncate">{{ $po->supplier }}</td>
                    <td class="px-6 py-4 max-w-[140px] truncate">{{ $po->item_name ?? '—' }}</td>
                    <td class="px-6 py-4">{{ $po->qty ?? '—' }}</td>
                    <td class="px-6 py-4">₱{{ number_format($po->amount, 2) }}</td>
                    <td class="px-6 py-4">
                        @php $colors = ['Draft'=>'bg-gray-100 text-gray-700', 'Sent'=>'bg-blue-100 text-blue-700', 'Confirmed'=>'bg-yellow-100 text-yellow-700', 'Delivered'=>'bg-green-100 text-green-700', 'Cancelled'=>'bg-red-100 text-red-700']; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $colors[$po->status] ?? 'bg-gray-100' }}">{{ $po->status }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-1.5">
                            @if($po->status === 'Draft')
                            <form method="POST" action="{{ route('procurement.po.send', $po->id) }}" class="inline" onsubmit="return confirm('Send this PO to supplier?')">
                                @csrf @method('PATCH')
                                <button class="px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-md hover:bg-blue-100">Send</button>
                            </form>
                            @endif
                            @if($po->status === 'Sent')
                            <form method="POST" action="{{ route('procurement.po.confirm', $po->id) }}" class="inline" onsubmit="return confirm('Confirm this PO?')">
                                @csrf @method('PATCH')
                                <button class="px-2.5 py-1.5 text-xs font-medium text-yellow-700 bg-yellow-50 rounded-md hover:bg-yellow-100">Confirm</button>
                            </form>
                            @endif
                            @if($po->status === 'Confirmed')
                            <form method="POST" action="{{ route('procurement.po.deliver', $po->id) }}" class="inline" onsubmit="return confirm('Mark as delivered?')">
                                @csrf @method('PATCH')
                                <button class="px-2.5 py-1.5 text-xs font-medium text-green-700 bg-green-50 rounded-md hover:bg-green-100">Deliver</button>
                            </form>
                            @endif
                            @if(in_array($po->status, ['Draft', 'Sent', 'Confirmed']))
                            <form method="POST" action="{{ route('procurement.po.cancel', $po->id) }}" class="inline" onsubmit="return confirm('Cancel this PO?')">
                                @csrf @method('PATCH')
                                <button class="px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 rounded-md hover:bg-red-100">Cancel</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No purchase orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Create PO Modal -->
<div id="createPOModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center" onclick="if(event.target===this)closeModal('createPOModal')">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold">New Purchase Order</h3>
            <button type="button" onclick="closeModal('createPOModal')" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form method="POST" action="{{ route('procurement.po.store') }}" class="p-6">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                    <input type="text" name="supplier" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                    <input type="text" name="item_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Qty</label>
                    <input type="number" name="qty" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Cost</label>
                    <input type="number" name="unit_cost" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                    <input type="number" name="amount" min="0" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Date *</label>
                    <input type="date" name="order_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery</label>
                    <input type="date" name="expected_delivery" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeModal('createPOModal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Create PO</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Modal helpers already in account-payable.js; inline for direct page support
function openModal(id) { document.getElementById(id)?.classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id)?.classList.add('hidden'); }
</script>
@endpush
