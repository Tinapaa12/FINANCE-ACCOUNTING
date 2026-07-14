@extends('layouts.app')

@section('page-title', 'Goods Received Notes')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold">Goods Received Notes</h2>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">GRN No.</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">PO No.</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ordered</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Received</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Received Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
@forelse($grns as $grn)
<tr class="hover:bg-gray-50 transition-colors">
    <td class="px-4 py-4 border-t border-gray-100 text-sm font-medium">{{ $grn->grn_no }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $grn->purchaseOrder?->po_no ?? 'N/A' }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $grn->item_name ?? $grn->purchaseOrder?->item_name ?? 'N/A' }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $grn->qty_ordered ?? ($grn->purchaseOrder?->qty ?? 'N/A') }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $grn->qty_received ?? ($grn->purchaseOrder?->qty ?? 'N/A') }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $grn->supplier }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">₱{{ number_format($grn->amount, 2) }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $grn->received_date->format('M d, Y') }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">
        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
            @if(strtolower($grn->status) === 'completed') bg-green-100 text-green-800
            @else bg-purple-100 text-purple-800 @endif">
            {{ $grn->status }}
        </span>
    </td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">
        @if($grn->status === 'Pending')
        <form action="{{ route('goods-received-notes.complete', $grn->id) }}" method="POST" class="inline">
            @csrf @method('PATCH')
            <button type="submit" class="border-none cursor-pointer rounded-md bg-green-600 text-white px-2 py-1 text-xs font-medium hover:bg-green-700 transition-colors">Complete</button>
        </form>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="10" class="text-center px-4 py-8 text-gray-500">No goods received notes found.</td>
</tr>
@endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $grns->links() }}
        </div>
    </div>

</div>

<!-- Add GRN Modal -->
<div id="grnModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
    <div class="w-[480px] bg-white rounded-xl shadow-xl p-8">
        <h2 class="text-xl font-bold mb-6">Add Goods Received Note</h2>
        <form method="POST" action="{{ route('goods-received-notes.store') }}">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4 col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Order :</label>
                    <select name="purchase_order_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">None (Manual Entry)</option>
                        @foreach($purchaseOrders as $po)
                        <option value="{{ $po->id }}">{{ $po->po_no }} - {{ $po->supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4 col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier :</label>
                    <input type="text" name="supplier" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount :</label>
                    <input type="number" name="amount" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Received Date :</label>
                    <input type="date" name="received_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status :</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Pending">Pending</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="mb-4 col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes :</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeGRNModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Add GRN</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit GRN Modal -->
<div id="editGRNModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
    <div class="w-[480px] bg-white rounded-xl shadow-xl p-8">
        <h2 class="text-xl font-bold mb-6">Edit Goods Received Note</h2>
        <form id="editGRNForm" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4 col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Order :</label>
                    <select name="purchase_order_id" id="editGRNPO" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">None (Manual Entry)</option>
                        @foreach($purchaseOrders as $po)
                        <option value="{{ $po->id }}">{{ $po->po_no }} - {{ $po->supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4 col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier :</label>
                    <input type="text" name="supplier" id="editGRNSupplier" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount :</label>
                    <input type="number" name="amount" id="editGRNAmount" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Received Date :</label>
                    <input type="date" name="received_date" id="editGRNDate" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status :</label>
                    <select name="status" id="editGRNStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Pending">Pending</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="mb-4 col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes :</label>
                    <textarea name="notes" id="editGRNNotes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeEditGRNModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openGRNModal() { document.getElementById('grnModal').classList.add('active'); }
function closeGRNModal() { document.getElementById('grnModal').classList.remove('active'); }
document.getElementById('grnModal')?.addEventListener('click', function(e) { if (e.target === this) closeGRNModal(); });

function editGRN(id, supplier, amount, receivedDate, poId, status, notes) {
    document.getElementById('editGRNSupplier').value = supplier;
    document.getElementById('editGRNAmount').value = amount;
    document.getElementById('editGRNDate').value = receivedDate;
    document.getElementById('editGRNPO').value = poId;
    document.getElementById('editGRNStatus').value = status;
    document.getElementById('editGRNNotes').value = notes;
    document.getElementById('editGRNForm').action = '/goods-received-notes/' + id;
    document.getElementById('editGRNModal').classList.add('active');
}
function closeEditGRNModal() { document.getElementById('editGRNModal').classList.remove('active'); }
document.getElementById('editGRNModal')?.addEventListener('click', function(e) { if (e.target === this) closeEditGRNModal(); });
</script>

@endsection
