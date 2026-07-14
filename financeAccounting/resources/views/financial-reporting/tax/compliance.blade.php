@extends('layouts.app')

@section('title', 'Tax and Compliance')

@section('page-title', 'Tax and Compliance')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500">Period:</span>
            <select class="border rounded px-3 py-1.5 text-sm" onchange="window.location.href='?period='+this.value">
                @foreach($periods as $p)
                    <option value="{{ $p }}" @selected($p === $period)>{{ $p }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-lg border p-5 text-center">
                <p class="text-gray-500 text-xs mb-1">Total Taxable Amount</p>
                <p class="font-bold text-2xl">₱{{ number_format($summary['total_taxable']) }}</p>
            </div>
            <div class="bg-white rounded-lg border p-5 text-center">
                <p class="text-gray-500 text-xs mb-1">Total Tax Computed</p>
                <p class="font-bold text-2xl">₱{{ number_format($summary['total_tax']) }}</p>
            </div>
            <div class="bg-white rounded-lg border p-5 text-center">
                <p class="text-gray-500 text-xs mb-1">Total Tax Paid / Filed</p>
                <p class="font-bold text-2xl">₱{{ number_format($summary['total_filed']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg border p-5">
            <h2 class="font-semibold text-lg mb-4">Tax Records</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="py-3 px-4 font-medium">Reference</th>
                            <th class="py-3 px-4 font-medium">Tax Type</th>
                            <th class="py-3 px-4 font-medium text-right">Taxable Amount</th>
                            <th class="py-3 px-4 font-medium text-right">Rate</th>
                            <th class="py-3 px-4 font-medium text-right">Tax Amount</th>
                            <th class="py-3 px-4 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($taxRecords as $record)
                            <tr class="border-b last:border-0 hover:bg-gray-50">
                                <td class="py-3 px-4">{{ $record['reference_type'] }} #{{ $record['reference_id'] }}</td>
                                <td class="py-3 px-4">{{ $record['tax_type'] }}</td>
                                <td class="py-3 px-4 text-right">₱{{ number_format($record['taxable_amount']) }}</td>
                                <td class="py-3 px-4 text-right">{{ rtrim(rtrim(number_format($record['tax_rate'], 2), '0'), '.') }}%</td>
                                <td class="py-3 px-4 text-right">₱{{ number_format($record['tax_amount']) }}</td>
                                <td class="py-3 px-4">
                                    @php
                                        $statusColors = [
                                            'filed' => 'bg-blue-100 text-blue-800',
                                            'paid' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                        ];
                                    @endphp
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $statusColors[$record['filing_status']] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($record['filing_status']) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
