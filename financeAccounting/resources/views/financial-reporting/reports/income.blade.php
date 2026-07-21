{{-- resources/views/reports/income.blade.php --}}
@extends('layouts.app')

@section('title', 'Income Statements')

@section('content')
    @if(empty($revenue) && empty($expenses))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <p class="text-yellow-800 font-medium">No data yet.</p>
            <p class="text-yellow-600 text-sm mt-1">Add journal entries with Revenue and Expense accounts first.</p>
        </div>
    @else
    <div class="w-full">
        <div class="bg-white rounded-lg border p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-semibold text-lg">Income statements</h2>
                    <p class="text-xs text-gray-500">
                        Period: {{ $selectedPeriod ?? 'All periods' }}
                    </p>
                </div>
                <select class="border rounded px-3 py-1.5 text-sm" onchange="window.location.href='?period='+this.value">
                    @foreach($periods as $p)
                        <option value="{{ $p }}" @selected($p === $selectedPeriod)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Revenue --}}
            <div class="bg-green-50 rounded-lg p-4 mb-4">
                <p class="font-semibold text-green-900 mb-2">Revenue Earned</p>
                @php $totalRevenue = 0; @endphp
                @foreach($revenue as $item)
                    @php $totalRevenue += $item['amount']; @endphp
                    <div class="flex justify-between text-sm text-green-900 py-1">
                        <span>{{ $item['label'] }}</span>
                        <span>₱{{ number_format($item['amount']) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between font-semibold text-green-900 mt-3 pt-3 border-t border-green-200">
                    <span>Total Revenue</span>
                    <span>₱{{ number_format($totalRevenue) }}</span>
                </div>
            </div>

            {{-- Expenses --}}
            <div class="bg-red-50 rounded-lg p-4">
                <p class="font-semibold text-red-900 mb-2">Expenses</p>
                @php $totalExpenses = 0; @endphp
                @foreach($expenses as $item)
                    @php $totalExpenses += $item['amount']; @endphp
                    <div class="flex justify-between text-sm text-red-900 py-1">
                        <span>{{ $item['label'] }}</span>
                        <span>₱{{ number_format($item['amount']) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between font-semibold text-red-900 mt-3 pt-3 border-t border-red-200">
                    <span>Total Expenses</span>
                    <span>₱{{ number_format($totalExpenses) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection