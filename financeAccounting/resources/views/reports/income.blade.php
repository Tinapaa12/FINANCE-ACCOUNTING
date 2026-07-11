@extends('layouts.app')

@section('title', 'Income Statement')

@section('page-title', 'Income Statement')

@section('content')
    <div class="flex flex-col lg:flex-row gap-6">
        <div class="flex-1">
            <div class="bg-white rounded-lg border p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-lg">Income Statement</h2>
                    <span class="text-sm text-gray-500">{{ $month ?? '' }}</span>
                </div>

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

                <div class="bg-red-50 rounded-lg p-4 mb-4">
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

                <div class="flex justify-between font-bold text-lg pt-4 border-t-2 border-slate-900">
                    <span>Net Income</span>
                    <span class="{{ ($totalRevenue - $totalExpenses) < 0 ? 'text-red-500' : 'text-green-600' }}">
                        ₱{{ number_format($totalRevenue - $totalExpenses) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-96">
            <div class="bg-white rounded-lg border p-5">
                <h2 class="font-semibold text-lg mb-4">Trial Balance</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-gray-500">
                                <th class="py-2 font-medium">Account</th>
                                <th class="py-2 font-medium text-right">Debit</th>
                                <th class="py-2 font-medium text-right">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trialBalance as $row)
                                <tr class="border-b last:border-0">
                                    <td class="py-2">{{ $row['account'] }}</td>
                                    <td class="py-2 text-right">₱{{ number_format($row['debit']) }}</td>
                                    <td class="py-2 text-right">₱{{ number_format($row['credit']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
