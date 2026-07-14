{{-- resources/views/reports/income.blade.php --}}
@extends('layouts.app')

@section('title', 'Income Statements')

@section('content')
    @if(!$report)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <p class="text-yellow-800 font-medium">No data yet.</p>
            <p class="text-yellow-600 text-sm mt-1">Add your first income statement data from the <a href="{{ route('reports.manage') }}" class="underline">Manage Data</a> page.</p>
        </div>
    @else
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- LEFT: Income Statement --}}
        <div class="flex-1">
            <div class="bg-white rounded-lg border p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="font-semibold text-lg">Income statements</h2>
                        <p class="text-xs text-gray-500">
                            Period: {{ $report->report_period_start->format('M d, Y') }} — {{ $report->report_period_end->format('M d, Y') }}
                        </p>
                    </div>
                    <select class="border rounded px-3 py-1.5 text-sm" onchange="window.location.href='?report_id='+this.value">
                        @foreach($reports as $r)
                            <option value="{{ $r->report_id }}" @selected($r->report_id === $selectedReportId)>
                                {{ $r->report_period_start->format('F Y') }}
                            </option>
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
                        <span>Total expenses</span>
                        <span>₱{{ number_format($totalExpenses) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Trial Balance --}}
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
    @endif
@endsection