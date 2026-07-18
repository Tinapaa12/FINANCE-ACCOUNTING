@extends('layouts.app')

@section('page-title', '3-Way Matching')

@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Match Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Perform 3-Way Match</h2>
            <p class="text-sm text-gray-500">Match PO + Goods Receipt + Invoice</p>
        </div>
        <form method="POST" action="{{ route('procurement.matching.match') }}" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Order</label>
                <select name="purchase_order_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select —</option>
                    @foreach($purchaseOrders as $po)
                    <option value="{{ $po->id }}">{{ $po->po_no }} — ₱{{ number_format($po->amount, 2) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Goods Received Note</label>
                <select name="goods_received_note_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select —</option>
                    @foreach($grns as $grn)
                    <option value="{{ $grn->id }}">{{ $grn->grn_no }} — {{ $grn->supplier }} (Qty: {{ $grn->qty_received }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Invoice</label>
                <select name="supplier_bill_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select —</option>
                    @foreach($bills as $bill)
                    <option value="{{ $bill->id }}">{{ $bill->bill_no }} — {{ $bill->supplier }} — ₱{{ number_format($bill->amount, 2) }} ({{ $bill->matching_status }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">Run 3-Way Match</button>
        </form>
    </div>

    <!-- Matched Results -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Matched Invoices</h2>
            <p class="text-sm text-gray-500">Ready for Accounts Payable</p>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($matched as $bill)
            <div class="px-6 py-4 flex justify-between items-center">
                <div>
                    <p class="font-medium text-sm">{{ $bill->bill_no }}</p>
                    <p class="text-xs text-gray-500">{{ $bill->supplier }} — ₱{{ number_format($bill->amount, 2) }}</p>
                </div>
                <a href="{{ route('supplier-bills.index', ['tab' => 'bills']) }}" class="text-xs px-3 py-1.5 bg-green-100 text-green-800 rounded-full font-medium hover:bg-green-200 no-underline">Go to AP</a>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-gray-500 text-sm">No matched invoices yet.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Unmatched / Flagged -->
@if($bills->whereIn('matching_status', ['Flagged', 'Partially Matched'])->count() > 0)
<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-red-700">Flagged / Mismatched</h2>
    </div>
    <div class="divide-y divide-gray-100">
        @foreach($bills->whereIn('matching_status', ['Flagged', 'Partially Matched']) as $bill)
        <div class="px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="font-medium text-sm">{{ $bill->bill_no }} — {{ $bill->supplier }}</p>
                    <p class="text-xs text-red-600">{{ $bill->matching_notes }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs px-3 py-1 rounded-full font-medium
                        @if($bill->matching_status === 'Flagged') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 @endif">{{ $bill->matching_status }}</span>
                    <form method="POST" action="{{ route('procurement.matching.resolve', $bill->id) }}" onsubmit="return confirm('Mark this bill as Matched?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-xs px-3 py-1.5 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700">Mark as Matched</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
