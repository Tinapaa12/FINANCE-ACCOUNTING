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
            <h2 class="font-semibold text-lg mb-4">
                Tax Records
                <span class="relative group inline-block ml-1.5 align-middle">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold cursor-help">!</span>
                    <div class="absolute left-0 top-6 z-50 hidden group-hover:block w-96 bg-gray-900 text-white text-xs rounded-lg shadow-xl p-4 leading-relaxed">
                        <p class="font-semibold mb-1.5 text-blue-300">Tax data is collected from 4 sources:</p>

                        <p class="mb-1">
                            <span class="text-yellow-300 font-medium">① GL Tax Accounts</span> (%VAT% / %Tax%)
                            <br><span class="ml-2 text-gray-300">Tax = max(Credit − Debit, 0) for Credit accounts</span>
                            <br><span class="ml-2 text-gray-300">Tax = max(Debit − Credit, 0) for Debit accounts</span>
                            <br><span class="ml-2 text-gray-300">Rate = Tax ÷ Taxable Amount × 100</span>
                        </p>

                        <p class="mb-1">
                            <span class="text-yellow-300 font-medium">② Sales Transactions</span> (VAT on sales)
                            <br><span class="ml-2 text-gray-300">VAT = Gross Amount × 0.12 ÷ 1.12</span>
                        </p>

                        <p class="mb-1">
                            <span class="text-yellow-300 font-medium">③ Purchase Orders</span> (VAT on purchases)
                            <br><span class="ml-2 text-gray-300">VAT = PO Amount × 12%</span>
                        </p>

                        <p class="mb-1">
                            <span class="text-yellow-300 font-medium">④ Supplier Bills</span> (EWT/VAT)
                            <br><span class="ml-2 text-gray-300">Tax = Bill Amount × EWT Rate ÷ 100</span>
                        </p>

                        <p class="mt-1.5 border-t border-gray-600 pt-1.5">
                            <span class="text-blue-300">Summary:</span> Total Taxable = Σ all taxable amounts
                            <br><span class="ml-14">Total Tax</span> = Σ all computed tax amounts
                            <br><span class="ml-14">Filed</span> = Σ tax where status = filed/paid
                        </p>
                    </div>
                </span>
            </h2>
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
                        @forelse($taxRecords as $record)
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
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">
                                    No tax transactions this period. Add journal entries with VAT/Tax accounts first.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
