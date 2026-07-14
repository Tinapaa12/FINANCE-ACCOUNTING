@extends('layouts.app')

@section('page-title', 'Audit Trail')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold">Audit Log</h2>
    </div>

    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search action, description, user..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <select name="model" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">All Models</option>
                <option value="SupplierBill" @selected(request('model') === 'SupplierBill')>Supplier Bill</option>
                <option value="PurchaseOrder" @selected(request('model') === 'PurchaseOrder')>Purchase Order</option>
                <option value="GoodsReceivedNote" @selected(request('model') === 'GoodsReceivedNote')>Goods Received Note</option>
                <option value="Payment" @selected(request('model') === 'Payment')>Payment</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">Filter</button>
        </form>
    </div>

    <div class="p-6">
        <table class="w-full border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Model</th>
                </tr>
            </thead>
            <tbody>
@forelse($logs as $log)
<tr class="hover:bg-gray-50 transition-colors">
    <td class="px-4 py-4 border-t border-gray-100 text-sm whitespace-nowrap">{{ $log->created_at->format('M d, Y h:i A') }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $log->user ?? '-' }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">
        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
            @if($log->action === 'created') bg-green-100 text-green-800
            @elseif($log->action === 'updated') bg-blue-100 text-blue-800
            @elseif($log->action === 'deleted') bg-red-100 text-red-800
            @else bg-gray-100 text-gray-800 @endif">
            {{ $log->action }}
        </span>
    </td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $log->description }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ class_basename($log->loggable_type) }} #{{ $log->loggable_id }}</td>
</tr>
@empty
<tr><td colspan="5" class="text-center px-4 py-8 text-gray-500">No audit logs found.</td></tr>
@endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $logs->appends(request()->query())->links() }}</div>
    </div>
</div>

@endsection
