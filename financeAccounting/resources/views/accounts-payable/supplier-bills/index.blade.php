@extends('layouts.app')

@section('page-title', $tab === 'bills' ? 'Supplier Bills' : ($tab === 'pos' ? 'Purchase Orders' : 'Goods Received Notes'))

@section('content')

<!-- Summary Cards -->
<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
    <h2 class="text-lg font-semibold mb-4">Account Payable Summary</h2>
    <div class="grid grid-cols-5 gap-4">
        <div class="p-4 rounded-xl bg-orange-50 border border-orange-200">
            <p class="text-sm font-medium text-orange-800">Total Bills</p>
            <p class="text-xl font-bold text-orange-900 mt-1">₱{{ number_format($totalBillsAmount, 2) }}</p>
            <p class="text-xs text-orange-600">{{ $totalBillsCount }} Bills</p>
        </div>
        <div class="p-4 rounded-xl bg-yellow-50 border border-yellow-200">
            <p class="text-sm font-medium text-yellow-800">Paid This Month</p>
            <p class="text-xl font-bold text-yellow-900 mt-1" id="paidMonthAmount">₱{{ number_format($paidThisMonthAmount, 2) }}</p>
            <p class="text-xs text-yellow-600" id="paidMonthCount">{{ $paidThisMonthCount }} Payments</p>
        </div>
        <div class="p-4 rounded-xl bg-green-50 border border-green-200">
            <p class="text-sm font-medium text-green-800">Payments Today</p>
            <p class="text-xl font-bold text-green-900 mt-1" id="paidTodayAmount">₱{{ number_format($paymentsTodayAmount, 2) }}</p>
            <p class="text-xs text-green-600" id="paidTodayCount">{{ $paymentsTodayCount }} Payments</p>
        </div>
        <div class="p-4 rounded-xl bg-purple-50 border border-purple-200">
            <p class="text-sm font-medium text-purple-800">Total Bill Pending</p>
            <p class="text-xl font-bold text-purple-900 mt-1" id="pendingAmount">₱{{ number_format($pendingBillsAmount, 2) }}</p>
            <p class="text-xs text-purple-600" id="pendingCount">{{ $pendingBillsCount }} Bills</p>
        </div>
        <div class="p-4 rounded-xl bg-red-50 border border-red-200">
            <p class="text-sm font-medium text-red-800">Overdue Bills</p>
            <p class="text-xl font-bold text-red-900 mt-1" id="overdueAmount">₱{{ number_format($overdueAmount, 2) }}</p>
            <p class="text-xs text-red-600" id="overdueCount">{{ $overdueCount }} Overdue</p>
        </div>
    </div>
</div>

<div class="flex gap-4 mt-4">

<div class="flex-1 min-w-0">
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="flex border-b border-gray-200">
        <a href="{{ route('supplier-bills.index', ['tab' => 'bills']) }}" class="px-6 py-4 text-sm font-medium no-underline {{ $tab === 'bills' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50/50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">Supplier Bills</a>
        <a href="{{ route('supplier-bills.index', ['tab' => 'pos']) }}" class="px-6 py-4 text-sm font-medium no-underline {{ $tab === 'pos' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50/50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">Purchase Orders</a>
        <a href="{{ route('supplier-bills.index', ['tab' => 'grns']) }}" class="px-6 py-4 text-sm font-medium no-underline {{ $tab === 'grns' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50/50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">Goods Received Notes</a>
    </div>

@if($tab === 'bills')
    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold">Supplier Bills</h2>
        <div class="flex gap-3 items-center">
            <form method="GET" action="{{ route('supplier-bills.index') }}" class="flex flex-wrap gap-2 items-center">
                <input type="hidden" name="tab" value="bills">
                <input type="text" name="search" placeholder="Search bills..." value="{{ $search ?? '' }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-44 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <select name="filter_status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="Pending" {{ $filterStatus === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ $filterStatus === 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Paid" {{ $filterStatus === 'Paid' ? 'selected' : '' }}>Paid</option>
                </select>
                <select name="filter_method" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Methods</option>
                    <option value="Cash" {{ $filterMethod === 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Check" {{ $filterMethod === 'Check' ? 'selected' : '' }}>Check</option>
                    <option value="Bank Transfer" {{ $filterMethod === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Search</button>
                @if(request('search') || request('filter_status') || request('filter_method'))
                    <a href="{{ route('supplier-bills.index', ['tab' => 'bills']) }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm no-underline hover:bg-gray-600">Clear</a>
                @endif
            </form>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors" onclick="openModal()">Add Bill</button>
        </div>
    </div>

    <div class="w-full overflow-x-auto">

        <table class="w-full border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-[10%] px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><a href="{{ route('supplier-bills.index', ['sort' => 'bill_no', 'direction' => $sort === 'bill_no' && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search, 'tab' => 'bills']) }}" class="text-inherit no-underline hover:text-gray-900">Bill No. @if($sort === 'bill_no') {{ $direction === 'asc' ? '▲' : '▼' }} @endif</a></th>
                    <th class="w-[10%] px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><a href="{{ route('supplier-bills.index', ['sort' => 'po_no', 'direction' => $sort === 'po_no' && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search, 'tab' => 'bills']) }}" class="text-inherit no-underline hover:text-gray-900">PO No. @if($sort === 'po_no') {{ $direction === 'asc' ? '▲' : '▼' }} @endif</a></th>
                    <th class="w-[11%] px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><a href="{{ route('supplier-bills.index', ['sort' => 'grn_no', 'direction' => $sort === 'grn_no' && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search, 'tab' => 'bills']) }}" class="text-inherit no-underline hover:text-gray-900">GRN No. @if($sort === 'grn_no') {{ $direction === 'asc' ? '▲' : '▼' }} @endif</a></th>
                    <th class="w-[15%] px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><a href="{{ route('supplier-bills.index', ['sort' => 'supplier', 'direction' => $sort === 'supplier' && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search, 'tab' => 'bills']) }}" class="text-inherit no-underline hover:text-gray-900">Supplier @if($sort === 'supplier') {{ $direction === 'asc' ? '▲' : '▼' }} @endif</a></th>
                    <th class="w-[9%] px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><a href="{{ route('supplier-bills.index', ['sort' => 'amount', 'direction' => $sort === 'amount' && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search, 'tab' => 'bills']) }}" class="text-inherit no-underline hover:text-gray-900">Amount @if($sort === 'amount') {{ $direction === 'asc' ? '▲' : '▼' }} @endif</a></th>
                    <th class="w-[11%] px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Balance</th>
                    <th class="w-[8%] px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><a href="{{ route('supplier-bills.index', ['sort' => 'due_date', 'direction' => $sort === 'due_date' && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search, 'tab' => 'bills']) }}" class="text-inherit no-underline hover:text-gray-900">Due @if($sort === 'due_date') {{ $direction === 'asc' ? '▲' : '▼' }} @endif</a></th>
                    <th class="w-[8%] px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><a href="{{ route('supplier-bills.index', ['sort' => 'status', 'direction' => $sort === 'status' && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search, 'tab' => 'bills']) }}" class="text-inherit no-underline hover:text-gray-900">Status @if($sort === 'status') {{ $direction === 'asc' ? '▲' : '▼' }} @endif</a></th>
                    <th class="w-[17%] px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
@forelse($supplierBills as $bill)
@php
    $attachJson = json_encode($bill->attachments->map(function($a) { return ['id' => $a->id, 'original_filename' => $a->original_filename]; })->values());
    $payJson = json_encode($bill->payments->map(function($p) { return ['amount' => $p->amount, 'method' => $p->payment_method, 'date' => $p->payment_date, 'reference' => $p->reference]; })->values());
@endphp
<tr class="hover:bg-gray-50 transition-colors"
    data-id="{{ $bill->id }}"
    data-bill-no="{{ $bill->bill_no }}"
    data-po-no="{{ $bill->po_no }}"
    data-grn-no="{{ $bill->grn_no }}"
    data-supplier="{{ $bill->supplier }}"
    data-amount="{{ $bill->amount }}"
    data-due-date="{{ $bill->due_date }}"
    data-status="{{ $bill->status }}"
    data-payment-method="{{ $bill->payment_method ?? '' }}"
    data-paid-at="{{ $bill->paid_at ? \Carbon\Carbon::parse($bill->paid_at)->format('M d, Y h:i A') : '' }}"
    data-ewt-rate="{{ $bill->ewt_rate ?? '' }}"
    data-payment-terms="{{ $bill->payment_terms ?? '' }}">
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $bill->bill_no }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $bill->po_no }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $bill->grn_no }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $bill->supplier }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">₱{{ number_format($bill->amount, 2) }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">
        <span class="{{ $bill->balance > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
            ₱{{ number_format($bill->balance, 2) }}
        </span>
    </td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $bill->due_date }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">
        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
            @if(strtolower($bill->status) === 'paid') bg-green-100 text-green-800
            @elseif(strtolower($bill->status) === 'approved') bg-yellow-100 text-yellow-800
            @else bg-purple-100 text-purple-800 @endif">
            {{ $bill->status }}
        </span>
    </td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">
        <div class="flex gap-1.5 items-center flex-wrap">
            <button type="button" class="border-none cursor-pointer rounded-md bg-blue-500 text-white w-7 h-7 flex items-center justify-center hover:bg-blue-600 transition-colors"
                onclick="openViewModal(
                    {{ $bill->id }},
                    '{{ $bill->supplier }}',
                    {{ $bill->amount }},
                    '{{ $bill->due_date }}',
                    '{{ $bill->status }}',
                    '{{ $bill->payment_method ?? '' }}',
                    '{{ $bill->bill_no ?? '' }}',
                    '{{ $bill->po_no ?? '' }}',
                    '{{ $bill->grn_no ?? '' }}',
                    {!! $attachJson !!},
                    {!! $payJson !!},
                    '{{ $bill->ewt_rate ?? '' }}',
                    '{{ $bill->payment_terms ?? '' }}'
                )" title="View">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
            @if($bill->status === 'Pending')
            <form action="{{ route('supplier-bills.approve', $bill->id) }}" method="POST" class="inline">
                @csrf @method('PATCH')
                <button type="submit" class="border-none cursor-pointer rounded-md bg-indigo-500 text-white px-2 py-1 text-xs font-medium hover:bg-indigo-600 transition-colors">Approve</button>
            </form>
            @endif
            <button class="border-none cursor-pointer rounded-md bg-orange-500 text-white w-7 h-7 flex items-center justify-center hover:bg-orange-600 transition-colors"
                onclick="editBill(
                    {{ $bill->id }},
                    '{{ $bill->supplier }}',
                    {{ $bill->amount }},
                    '{{ $bill->due_date }}',
                    '{{ $bill->status }}',
                    '{{ $bill->payment_method ?? '' }}',
                    '{{ $bill->ewt_rate ?? '' }}',
                    '{{ $bill->payment_terms ?? '' }}'
                )" title="Edit">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
            </button>
            <form action="{{ route('supplier-bills.destroy', $bill->id) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="border-none cursor-pointer rounded-md bg-red-500 text-white w-7 h-7 flex items-center justify-center hover:bg-red-600 transition-colors"
                    onclick="return confirm('Delete this bill?')" title="Delete">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                </button>
            </form>
            @if($bill->status === 'Approved' && $bill->balance > 0)
            <button type="button" onclick="openPaymentModal({{ $bill->id }}, {{ $bill->amount }}, {{ $bill->total_paid }}, '{{ $bill->payment_method ?? '' }}')"
                class="border-none cursor-pointer rounded-md bg-green-600 text-white px-2 py-1 text-xs font-medium hover:bg-green-700 transition-colors">Pay</button>
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="9" class="text-center px-4 py-8 text-gray-500">No supplier bills found.</td>
</tr>
@endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $supplierBills->appends(request()->query())->links() }}
        </div>

    </div>
@elseif($tab === 'pos')
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
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $purchaseOrders->links() }}
    </div>
@elseif($tab === 'grns')
    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold">Goods Received Notes</h2>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors" onclick="openGRNModal()">Add GRN</button>
    </div>
    <div class="w-full overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">GRN No.</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">PO No.</th>
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
        <div class="flex gap-2 items-center">
            <button class="border-none cursor-pointer rounded-md bg-orange-500 text-white w-8 h-8 flex items-center justify-center hover:bg-orange-600 transition-colors"
                onclick="editGRN({{ $grn->id }}, '{{ $grn->supplier }}', {{ $grn->amount }}, '{{ $grn->received_date->format('Y-m-d') }}', '{{ $grn->purchase_order_id }}', '{{ $grn->status }}', '{{ addslashes($grn->notes) }}')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
            </button>
            <form action="{{ route('goods-received-notes.destroy', $grn->id) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="border-none cursor-pointer rounded-md bg-red-500 text-white w-8 h-8 flex items-center justify-center hover:bg-red-600 transition-colors"
                    onclick="return confirm('Delete this GRN?')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center px-4 py-8 text-gray-500">No goods received notes found.</td>
</tr>
@endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $grns->links() }}
    </div>
@endif

</div>
</div>

@if($tab === 'bills')
<div class="w-80 shrink-0">
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <h2 class="text-lg font-semibold mb-4">Upcoming Supplier Bills</h2>
        <div id="upcomingBills">
        @foreach($upcomingBills as $bill)
        <div class="flex justify-between items-center py-3 border-b border-gray-200">
            <div>
                <h4 class="font-medium">{{ $bill->supplier }}</h4>
                <p class="text-sm text-gray-500">{{ $bill->bill_no }} • {{ \Carbon\Carbon::parse($bill->due_date)->format('M d') }}</p>
            </div>
            <div class="text-right">
                <span class="block font-semibold text-blue-600">₱{{ number_format($bill->amount, 2) }}</span>
                <span class="text-xs px-2 py-1 rounded-full text-white bg-purple-400">{{ \Carbon\Carbon::parse($bill->due_date)->diffForHumans() }}</span>
            </div>
        </div>
        @endforeach
        </div>
        <div class="flex justify-between mt-6 pt-4 border-t border-gray-200">
            <p class="font-medium">Total Outstanding :</p>
            <p class="text-xl font-bold" id="totalOutstanding">₱{{ number_format($totalBillsAmount,2) }}</p>
        </div>
        @if($overdueBills->count() > 0)
        <div class="mt-6 pt-4 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-red-600 mb-3">Overdue Bills</h3>
            <div id="overdueBills">
            @foreach($overdueBills as $bill)
            <div class="flex justify-between items-center py-2 border-b border-red-100">
                <div>
                    <h4 class="font-medium text-sm">{{ $bill->supplier }}</h4>
                    <p class="text-xs text-gray-500">{{ $bill->bill_no }}</p>
                </div>
                <div class="text-right">
                    <span class="block font-semibold text-red-600 text-sm">₱{{ number_format($bill->amount, 2) }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full text-white bg-red-500">{{ now()->diffInDays(\Carbon\Carbon::parse($bill->due_date)) }} days overdue</span>
                </div>
            </div>
            @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endif

</div>

<!-- Add Modal -->
<div id="billModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
    <div class="w-[420px] bg-white rounded-xl shadow-xl p-8">

        <h2 class="text-xl font-bold mb-6">Add Supplier Bill</h2>

        <form method="POST" action="{{ route('supplier-bills.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier :</label>
                <input type="text" name="supplier" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount :</label>
                <input type="number" name="amount" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date :</label>
                <input type="date" name="due_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method :</label>
                <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Method</option>
                    <option value="Cash">Cash</option>
                    <option value="Check">Check</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status :</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">EWT Rate (%) :</label>
                    <input type="number" name="ewt_rate" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Terms :</label>
                    <select name="payment_terms" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Terms</option>
                        <option value="Due on Receipt">Due on Receipt</option>
                        <option value="Net 15">Net 15</option>
                        <option value="Net 30">Net 30</option>
                        <option value="Net 60">Net 60</option>
                        <option value="Net 90">Net 90</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Add Bill</button>
            </div>

        </form>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
    <div class="w-[420px] bg-white rounded-xl shadow-xl p-8">

        <h2 class="text-xl font-bold mb-6">Bill Details</h2>

        <div class="space-y-4">
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Bill No.</span> <span id="viewBillNo" class="text-gray-900"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">PO No.</span> <span id="viewPoNo" class="text-gray-900"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Receipt/GRN No.</span> <span id="viewGrnNo" class="text-gray-900"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Supplier</span> <span id="viewSupplier" class="text-gray-900"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Amount</span> <span id="viewAmount" class="text-gray-900"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Due</span> <span id="viewDue" class="text-gray-900"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Payment Method</span> <span id="viewPaymentMethod" class="text-gray-900"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">EWT Rate</span> <span id="viewEwtRate" class="text-gray-900"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Payment Terms</span> <span id="viewTerms" class="text-gray-900"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Status</span> <span id="viewStatus" class="text-gray-900"></span></div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Payment History</h4>
            <div id="viewPayments" class="space-y-1"></div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Attachments</h4>
            <div id="viewAttachments" class="space-y-1"></div>
            <form id="uploadForm" method="POST" enctype="multipart/form-data" class="mt-3">
                @csrf
                <input type="file" name="file" id="uploadFileInput" class="block w-full text-xs text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <input type="hidden" name="bill_id" id="uploadBillId">
                <button type="submit" class="mt-2 text-xs px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Upload</button>
            </form>
        </div>

        <div class="flex justify-end mt-6">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeViewModal()">Close</button>
        </div>

    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
    <div class="w-[420px] bg-white rounded-xl shadow-xl p-8">

        <h2 class="text-xl font-bold mb-6">Edit Supplier Bill</h2>

        <form id="editBillForm" method="POST">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier :</label>
                <input type="text" name="supplier" id="editSupplier" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount :</label>
                <input type="number" name="amount" id="editAmount" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Due :</label>
                <input type="date" name="due_date" id="editDue" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method :</label>
                <select name="payment_method" id="editPaymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Method</option>
                    <option value="Cash">Cash</option>
                    <option value="Check">Check</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status :</label>
                <select name="status" id="editStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">EWT Rate (%) :</label>
                    <input type="number" name="ewt_rate" id="editEwtRate" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Terms :</label>
                    <select name="payment_terms" id="editPaymentTerms" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Terms</option>
                        <option value="Due on Receipt">Due on Receipt</option>
                        <option value="Net 15">Net 15</option>
                        <option value="Net 30">Net 30</option>
                        <option value="Net 60">Net 60</option>
                        <option value="Net 90">Net 90</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Save Changes</button>
            </div>

        </form>

    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
    <div class="w-[420px] bg-white rounded-xl shadow-xl p-8">
        <h2 class="text-xl font-bold mb-6">Record Payment</h2>
        <form method="POST" action="{{ route('payments.store') }}">
            @csrf
            <input type="hidden" name="supplier_bill_id" id="paymentBillId">
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-1">Bill Amount: <strong id="paymentBillAmount" class="text-gray-900"></strong></p>
                <p class="text-sm text-gray-600 mb-3">Remaining Balance: <strong id="paymentRemaining" class="text-red-600"></strong></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Amount :</label>
                <input type="number" name="amount" id="paymentAmount" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method :</label>
                <select name="payment_method" id="paymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Method</option>
                    <option value="Cash">Cash</option>
                    <option value="Check">Check</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date :</label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference (optional) :</label>
                <input type="text" name="reference" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closePaymentModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">Record Payment</button>
            </div>
        </form>
    </div>
</div>

<!-- PO Modals -->
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

<!-- GRN Modals -->
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
                        @foreach($allPOs as $po)
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
                        @foreach($allPOs as $po)
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
function openViewModal(id, supplier, amount, due, status, paymentMethod, billNo, poNo, grnNo, attachments, payments, ewtRate, terms) {
    document.getElementById('viewSupplier').textContent = supplier;
    document.getElementById('viewAmount').textContent = '₱' + Number(amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('viewDue').textContent = due;
    document.getElementById('viewStatus').textContent = status;
    document.getElementById('viewPaymentMethod').textContent = paymentMethod || '-';
    document.getElementById('viewEwtRate').textContent = ewtRate ? ewtRate + '%' : '-';
    document.getElementById('viewTerms').textContent = terms || '-';
    document.getElementById('viewBillNo').textContent = billNo || '-';
    document.getElementById('viewPoNo').textContent = poNo || '-';
    document.getElementById('viewGrnNo').textContent = grnNo || '-';
    document.getElementById('uploadForm').action = '/supplier-bills/' + id + '/attachments';
    document.getElementById('uploadBillId').value = id;

    var container = document.getElementById('viewAttachments');
    container.innerHTML = '';
    if (attachments && attachments.length) {
        attachments.forEach(function(a) {
            var link = document.createElement('a');
            link.href = '/attachments/' + a.id + '/download';
            link.className = 'block text-xs text-blue-600 hover:underline';
            link.textContent = a.original_filename;
            container.appendChild(link);
        });
    } else {
        container.innerHTML = '<p class="text-xs text-gray-400">No attachments.</p>';
    }

    var pContainer = document.getElementById('viewPayments');
    pContainer.innerHTML = '';
    if (payments && payments.length) {
        payments.forEach(function(p) {
            var row = document.createElement('div');
            row.className = 'flex justify-between items-center py-1.5 text-xs border-b border-gray-50';
            row.innerHTML = '<span>' + (p.date || '-') + '</span><span>₱' + Number(p.amount).toLocaleString(undefined, {minimumFractionDigits: 2}) + '</span><span class="text-gray-500">' + (p.method || '-') + '</span>';
            pContainer.appendChild(row);
        });
    } else {
        pContainer.innerHTML = '<p class="text-xs text-gray-400">No payments recorded.</p>';
    }

    document.getElementById('viewModal').classList.add('active');
}
function closeViewModal() { document.getElementById('viewModal').classList.remove('active'); }
document.getElementById('viewModal')?.addEventListener('click', function(e) { if (e.target === this) closeViewModal(); });

function openPaymentModal(id, amount, totalPaid, method) {
    const balance = amount - totalPaid;
    document.getElementById('paymentBillId').value = id;
    document.getElementById('paymentBillAmount').textContent = '₱' + Number(amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('paymentRemaining').textContent = '₱' + Number(balance).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('paymentAmount').value = '';
    document.getElementById('paymentAmount').max = balance;
    document.getElementById('paymentMethod').value = method || '';
    document.getElementById('paymentModal').classList.add('active');
}
function closePaymentModal() { document.getElementById('paymentModal').classList.remove('active'); }
document.getElementById('paymentModal')?.addEventListener('click', function(e) { if (e.target === this) closePaymentModal(); });

/* PO modals */
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

/* GRN modals */
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
