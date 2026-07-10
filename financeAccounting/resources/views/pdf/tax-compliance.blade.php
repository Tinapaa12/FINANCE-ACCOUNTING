{{-- resources/views/pdf/tax-compliance.blade.php --}}
@extends('layouts.pdf')

@section('title', 'Tax and Compliance PDF')
@section('pdf-title', 'Tax and Compliance Report')

@section('pdf-content')
    <p class="text-sm text-gray-500 mb-6">Period: {{ $period ?? '' }}</p>

    <div class="grid grid-cols-3 gap-4 mb-8 text-center text-sm">
        <div class="border rounded p-3">
            <p class="text-gray-500 text-xs mb-1">Total Taxable Amount</p>
            <p class="font-bold text-lg">₱{{ number_format($summary['total_taxable']) }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-gray-500 text-xs mb-1">Total Tax Computed</p>
            <p class="font-bold text-lg">₱{{ number_format($summary['total_tax']) }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-gray-500 text-xs mb-1">Total Tax Paid / Filed</p>
            <p class="font-bold text-lg">₱{{ number_format($summary['total_filed']) }}</p>
        </div>
    </div>

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b text-left text-gray-500">
                <th class="py-2 px-3 font-medium">Reference</th>
                <th class="py-2 px-3 font-medium">Tax Type</th>
                <th class="py-2 px-3 font-medium text-right">Taxable Amount</th>
                <th class="py-2 px-3 font-medium text-right">Rate</th>
                <th class="py-2 px-3 font-medium text-right">Tax Amount</th>
                <th class="py-2 px-3 font-medium">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($taxRecords as $record)
                <tr class="border-b last:border-0">
                    <td class="py-2 px-3">{{ $record['reference_type'] }} #{{ $record['reference_id'] }}</td>
                    <td class="py-2 px-3">{{ $record['tax_type'] }}</td>
                    <td class="py-2 px-3 text-right">₱{{ number_format($record['taxable_amount']) }}</td>
                    <td class="py-2 px-3 text-right">{{ rtrim(rtrim(number_format($record['tax_rate'], 2), '0'), '.') }}%</td>
                    <td class="py-2 px-3 text-right">₱{{ number_format($record['tax_amount']) }}</td>
                    <td class="py-2 px-3">{{ ucfirst($record['filing_status']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
