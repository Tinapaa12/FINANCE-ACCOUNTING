@extends('layouts.app')

@section('page-title', 'Supplier Bills')

@section('content')

<div class="flex gap-6 items-stretch">

    <div class="flex-[2] bg-white rounded-xl p-6 shadow-sm border border-gray-200">

        <h2 class="text-lg font-semibold mb-4">Upcoming Supplier Bills</h2>

        @foreach($upcomingBills as $bill)

        <div class="flex justify-between items-center py-3 border-b border-gray-200">

            <div>
                <h4 class="font-medium">{{ $bill->supplier }}</h4>
                <p class="text-sm text-gray-500">{{ $bill->bill_no }} • {{ \Carbon\Carbon::parse($bill->due_date)->format('M d') }}</p>
            </div>

            <div class="text-right">
                <span class="block font-semibold text-blue-600">
                    ₱{{ number_format($bill->amount, 2) }}
                </span>
                <span class="text-xs px-2 py-1 rounded-full text-white bg-purple-400">
                    {{ \Carbon\Carbon::parse($bill->due_date)->diffForHumans() }}
                </span>
            </div>

        </div>

        @endforeach

        <div class="flex justify-between mt-6 pt-4 border-t border-gray-200">
            <p class="font-medium">Total Outstanding :</p>
            <p class="text-xl font-bold">₱{{ number_format($totalBillsAmount,2) }}</p>
        </div>

    </div>

    <div class="w-[480px] min-w-[480px] bg-white rounded-xl p-6 shadow-sm border border-gray-200">

        <h2 class="text-lg font-semibold">Account Payable Summary</h2>

        <div class="grid grid-cols-2 gap-4 mt-5">

            <div class="p-5 rounded-xl bg-orange-50 border border-orange-200">
                <p class="text-sm font-medium text-orange-800">Total Bills</p>
                <p class="text-2xl font-bold text-orange-900 mt-2">₱{{ number_format($totalBillsAmount, 2) }}</p>
                <p class="text-sm text-orange-600">{{ $totalBillsCount }} Bills</p>
            </div>

            <div class="p-5 rounded-xl bg-yellow-50 border border-yellow-200">
                <p class="text-sm font-medium text-yellow-800">Paid This Month</p>
                <p class="text-2xl font-bold text-yellow-900 mt-2">₱{{ number_format($paidThisMonthAmount, 2) }}</p>
                <p class="text-sm text-yellow-600">{{ $paidThisMonthCount }} Payments</p>
            </div>

            <div class="p-5 rounded-xl bg-green-50 border border-green-200">
                <p class="text-sm font-medium text-green-800">Payments Today</p>
                <p class="text-2xl font-bold text-green-900 mt-2">₱{{ number_format($paymentsTodayAmount, 2) }}</p>
                <p class="text-sm text-green-600">{{ $paymentsTodayCount }} Payments</p>
            </div>

            <div class="p-5 rounded-xl bg-purple-50 border border-purple-200">
                <p class="text-sm font-medium text-purple-800">Total Bill Pending</p>
                <p class="text-2xl font-bold text-purple-900 mt-2">₱{{ number_format($pendingBillsAmount, 2) }}</p>
                <p class="text-sm text-purple-600">{{ $pendingBillsCount }} Bills</p>
            </div>

        </div>

    </div>

</div>

<div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">

        <h2 class="text-lg font-semibold">Supplier Bills</h2>

        <div class="flex gap-3 items-center">
            <form method="GET" action="{{ route('supplier-bills.index') }}" class="flex gap-2">
                <input type="text" name="search" placeholder="Search bills..." value="{{ $search ?? '' }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-56 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Search</button>
                @if(request('search'))
                    <a href="{{ route('supplier-bills.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm no-underline hover:bg-gray-600">Clear</a>
                @endif
            </form>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors" onclick="openModal()">Add Bill</button>
        </div>

    </div>

    <div class="w-full overflow-x-auto">

        <table class="w-full border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><a href="{{ route('supplier-bills.index', ['sort' => 'bill_no', 'direction' => $sort === 'bill_no' && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search]) }}" class="text-inherit no-underline hover:text-gray-900">Bill No. @if($sort === 'bill_no') {{ $direction === 'asc' ? '▲' : '▼' }} @endif</a></th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><a href="{{ route('supplier-bills.index', ['sort' => 'po_no', 'direction' => $sort === 'po_no' && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search]) }}" class="text-inherit no-underline hover:text-gray-900">PO No. @if($sort === 'po_no') {{ $direction === 'asc' ? '▲' : '▼' }} @endif</a></th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><a href="{{ route('supplier-bills.index', ['sort' => 'grn_no', 'direction' => $sort === 'grn_no' && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search]) }}" class="text-inherit no-underline hover:text-gray-900">Receipt/GRN No. @if($sort === 'grn_no') {{ $direction === 'asc' ? '▲' : '▼' }} @endif</a></th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><a href="{{ route('supplier-bills.index', ['sort' => 'supplier', 'direction' => $sort === 'supplier' && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search]) }}" class="text-inherit no-underline hover:text-gray-900">Supplier @if($sort === 'supplier') {{ $direction === 'asc' ? '▲' : '▼' }} @endif</a></th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><a href="{{ route('supplier-bills.index', ['sort' => 'amount', 'direction' => $sort === 'amount' && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search]) }}" class="text-inherit no-underline hover:text-gray-900">Amount @if($sort === 'amount') {{ $direction === 'asc' ? '▲' : '▼' }} @endif</a></th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><a href="{{ route('supplier-bills.index', ['sort' => 'due_date', 'direction' => $sort === 'due_date' && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search]) }}" class="text-inherit no-underline hover:text-gray-900">Due @if($sort === 'due_date') {{ $direction === 'asc' ? '▲' : '▼' }} @endif</a></th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><a href="{{ route('supplier-bills.index', ['sort' => 'status', 'direction' => $sort === 'status' && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search]) }}" class="text-inherit no-underline hover:text-gray-900">Status @if($sort === 'status') {{ $direction === 'asc' ? '▲' : '▼' }} @endif</a></th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
@forelse($supplierBills as $bill)
<tr class="hover:bg-gray-50 transition-colors">
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $bill->bill_no }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $bill->po_no }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $bill->grn_no }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $bill->supplier }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">₱{{ number_format($bill->amount, 2) }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $bill->due_date }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">
        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
            @if(strtolower($bill->status) === 'approved') bg-yellow-100 text-yellow-800
            @elseif(strtolower($bill->status) === 'paid') bg-green-100 text-green-800
            @else bg-purple-100 text-purple-800 @endif">
            {{ $bill->status }}
        </span>
    </td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">
        <div class="flex gap-2 items-center">
            <button class="border-none cursor-pointer rounded-md bg-orange-500 text-white w-8 h-8 flex items-center justify-center hover:bg-orange-600 transition-colors"
                onclick="editBill(
                    {{ $bill->id }},
                    '{{ $bill->supplier }}',
                    {{ $bill->amount }},
                    '{{ $bill->due_date }}',
                    '{{ $bill->status }}'
                )">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
            </button>

            <form action="{{ route('supplier-bills.destroy', $bill->id) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="border-none cursor-pointer rounded-md bg-red-500 text-white w-8 h-8 flex items-center justify-center hover:bg-red-600 transition-colors"
                    onclick="return confirm('Delete this bill?')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                </button>
            </form>

            <button class="border-none cursor-pointer rounded-md bg-blue-600 text-white px-3 py-1.5 text-xs font-medium hover:bg-blue-700 transition-colors" onclick="viewBill(this)">View</button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center px-4 py-8 text-gray-500">No supplier bills found.</td>
</tr>
@endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $supplierBills->appends(request()->query())->links() }}
        </div>

    </div>

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
                <label class="block text-sm font-medium text-gray-700 mb-1">Status :</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Paid">Paid</option>
                </select>
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
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Status</span> <span id="viewStatus" class="text-gray-900"></span></div>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Status :</label>
                <select name="status" id="editStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Save Changes</button>
            </div>

        </form>

    </div>
</div>

@endsection
