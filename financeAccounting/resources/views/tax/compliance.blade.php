@extends('layouts.app')

@section('title', 'Tax and Compliance')
@section('page-title', 'Tax and Compliance')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto mb-6">
        <div class="bg-white rounded-lg border p-6 text-center">
            <p class="text-gray-500 text-sm mb-1">Total Taxable Amount</p>
            <p class="text-2xl font-bold">₱{{ number_format($summary['total_taxable']) }}</p>
        </div>
        <div class="bg-white rounded-lg border p-6 text-center">
            <p class="text-gray-500 text-sm mb-1">Total Tax Computed</p>
            <p class="text-2xl font-bold text-indigo-600">₱{{ number_format($summary['total_tax']) }}</p>
        </div>
        <div class="bg-white rounded-lg border p-6 text-center">
            <p class="text-gray-500 text-sm mb-1">Total Tax Paid / Filed</p>
            <p class="text-2xl font-bold text-green-600">₱{{ number_format($summary['total_filed']) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg border p-5 max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-lg">Tax Calculations</h2>
            <select class="border rounded px-3 py-1.5 text-sm">
                <option>{{ $period ?? 'July 2026' }}</option>
            </select>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-2 font-medium">Reference</th>
                    <th class="py-2 font-medium">Tax Type</th>
                    <th class="py-2 font-medium">Taxable Amount</th>
                    <th class="py-2 font-medium">Rate</th>
                    <th class="py-2 font-medium">Tax Amount</th>
                    <th class="py-2 font-medium">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($taxRecords as $record)
                    <tr class="border-b last:border-0">
                        <td class="py-2.5">{{ $record['reference_type'] }} #{{ $record['reference_id'] }}</td>
                        <td class="py-2.5">{{ $record['tax_type'] }}</td>
                        <td class="py-2.5">₱{{ number_format($record['taxable_amount']) }}</td>
                        <td class="py-2.5">{{ rtrim(rtrim(number_format($record['tax_rate'], 2), '0'), '.') }}%</td>
                        <td class="py-2.5 font-medium">₱{{ number_format($record['tax_amount']) }}</td>
                        <td class="py-2.5">
                            @if($record['filing_status'] === 'paid')
                                <span class="inline-block bg-green-100 text-green-700 text-xs font-medium px-3 py-1 rounded-full">Paid ✓</span>
                            @elseif($record['filing_status'] === 'filed')
                                <span class="inline-block bg-blue-100 text-blue-700 text-xs font-medium px-3 py-1 rounded-full">Filed</span>
                            @else
                                <span class="inline-block bg-yellow-100 text-yellow-700 text-xs font-medium px-3 py-1 rounded-full">Pending</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-semibold border-t-2">
                    <td class="py-3" colspan="2">Totals</td>
                    <td class="py-3">₱{{ number_format($summary['total_taxable']) }}</td>
                    <td class="py-3"></td>
                    <td class="py-3">₱{{ number_format($summary['total_tax']) }}</td>
                    <td class="py-3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
