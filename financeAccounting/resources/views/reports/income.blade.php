@extends('layouts.app')

@section('title', 'Income Statements')
@section('page-title', 'Income Statements')

@section('content')
    <div class="flex flex-col lg:flex-row gap-6">
        <div class="flex-1">
            <div class="bg-white rounded-lg border p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-lg">Income statements</h2>
                    <select class="border rounded px-3 py-1.5 text-sm">
                        <option>{{ $month ?? 'September' }}</option>
                    </select>
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
                        <span>Total expenses</span>
                        <span>₱{{ number_format($totalExpenses) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-96">
            <div class="bg-white rounded-lg border p-5">
                <h2 class="font-semibold text-lg mb-4">Trial balance</h2>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 font-medium">Account</th>
                            <th class="py-2 font-medium">Credit</th>
                            <th class="py-2 font-medium">Debit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalCredit = 0; $totalDebit = 0; @endphp
                        @foreach($trialBalance as $row)
                            @php
                                $totalCredit += $row['credit'] ?? 0;
                                $totalDebit += $row['debit'] ?? 0;
                            @endphp
                            <tr class="border-b last:border-0">
                                <td class="py-2">{{ $row['account'] }}</td>
                                <td class="py-2">{{ $row['credit'] ? '₱'.number_format($row['credit']) : '--' }}</td>
                                <td class="py-2">{{ $row['debit'] ? '₱'.number_format($row['debit']) : '--' }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-semibold">
                            <td class="py-2">Totals</td>
                            <td class="py-2">₱{{ number_format($totalCredit) }}</td>
                            <td class="py-2">₱{{ number_format($totalDebit) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
