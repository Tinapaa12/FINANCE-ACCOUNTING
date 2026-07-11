@extends('layouts.app')

@section('page-title', 'Payments Made')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold">Payments Made to Suppliers</h2>
        <div>
            <input type="text" placeholder="Search Payment" class="w-80 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <table class="w-full border-collapse">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Payment ID</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Supplier</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Amount</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Payment Date</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Method</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $payment->bill_no }}</td>
                    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $payment->supplier }}</td>
                    <td class="px-4 py-4 border-t border-gray-100 text-sm">₱{{ number_format($payment->amount, 2) }}</td>
                    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') }}</td>
                    <td class="px-4 py-4 border-t border-gray-100 text-sm">Cash</td>
                    <td class="px-4 py-4 border-t border-gray-100 text-sm">
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                            @if(strtolower($payment->status) === 'paid') bg-green-100 text-green-800
                            @elseif(strtolower($payment->status) === 'approved') bg-yellow-100 text-yellow-800
                            @else bg-purple-100 text-purple-800 @endif">
                            {{ $payment->status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center px-4 py-8 text-gray-500">No payments made yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection
