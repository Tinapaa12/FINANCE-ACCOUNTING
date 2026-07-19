@extends('layouts.app')

@section('page-title', 'Goods Receipts')

@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold">Goods Received Notes</h2>
        <a href="{{ route('procurement.gr.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors no-underline">+ Receive Goods</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-6 py-3 font-medium text-gray-600">GRN #</th>
                    <th class="px-6 py-3 font-medium text-gray-600">PO Ref</th>
                    <th class="px-6 py-3 font-medium text-gray-600">Supplier</th>
                    <th class="px-6 py-3 font-medium text-gray-600">Item</th>
                    <th class="px-6 py-3 font-medium text-gray-600">Received</th>
                    <th class="px-6 py-3 font-medium text-gray-600">Qty</th>
                    <th class="px-6 py-3 font-medium text-gray-600">Status</th>
                    <th class="px-6 py-3 font-medium text-gray-600"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($grns as $grn)
                <tr>
                    <td class="px-6 py-4 font-mono text-xs">{{ $grn->grn_no }}</td>
                    <td class="px-6 py-4 font-mono text-xs">{{ $grn->po_no_ref ?? $grn->purchaseOrder?->po_no ?? '—' }}</td>
                    <td class="px-6 py-4 max-w-[140px] truncate">{{ $grn->supplier }}</td>
                    <td class="px-6 py-4 max-w-[140px] truncate">{{ $grn->item_name ?? '—' }}</td>
                    <td class="px-6 py-4">{{ $grn->received_date ? $grn->received_date->format('Y-m-d') : '—' }}</td>
                    <td class="px-6 py-4">{{ $grn->qty_received }}</td>
                    <td class="px-6 py-4">
                        @php $c = $grn->status === 'Completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $c }}">{{ $grn->status }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($grn->status !== 'Completed')
                        <form method="POST" action="{{ route('procurement.gr.complete', $grn->id) }}" class="inline" onsubmit="return confirm('Complete this GRN?')">
                            @csrf @method('PATCH')
                            <button class="px-2.5 py-1.5 text-xs font-medium text-green-700 bg-green-50 rounded-md hover:bg-green-100">Complete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-6 py-8 text-center text-gray-500">No goods received yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
