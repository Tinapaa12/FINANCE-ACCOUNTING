{{-- resources/views/reports/income.blade.php --}}
@extends('layouts.app')

@section('title', 'Income Statements')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg border p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-semibold text-lg">
                        Income Statement
                        <span class="relative group inline-block ml-1.5">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold cursor-help">!</span>
                            <div class="absolute left-0 top-6 z-50 hidden group-hover:block w-80 bg-gray-900 text-white text-xs rounded-lg shadow-xl p-4 leading-relaxed">
                                <p class="font-semibold mb-1.5 text-blue-300">How Income Statement is computed:</p>
                                <p class="mb-1">Queries <span class="text-yellow-300">Posted</span> journal entries in the selected period, grouped by account.</p>
                                <p class="mb-1">
                                    <span class="text-green-300">Revenue</span> accounts (normal balance = Credit):
                                    <br><span class="text-green-300 ml-2">Total = SUM(Credit)</span>
                                </p>
                                <p class="mb-1">
                                    <span class="text-red-300">Expense</span> accounts (normal balance = Debit):
                                    <br><span class="text-red-300 ml-2">Total = SUM(Debit)</span>
                                </p>
                                <p class="mt-2 border-t border-gray-600 pt-2">
                                    <span class="text-blue-300">Net Income</span> = Total Revenue − Total Expenses
                                </p>
                            </div>
                        </span>
                    </h2>
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

            @if(empty($revenue) && empty($expenses))
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <p class="text-yellow-800 font-medium">No data yet.</p>
                    <p class="text-yellow-600 text-sm mt-1">Add journal entries with Revenue and Expense accounts first.</p>
                </div>
            @else

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
        @endif
        </div>
    </div>
@endsection