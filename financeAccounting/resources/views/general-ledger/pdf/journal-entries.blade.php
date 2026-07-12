@extends('layouts.pdf')

@section('title', 'Journal Entries PDF')
@section('company-name', '')
@section('pdf-title', 'Journal Entries')

@section('pdf-content')
    @foreach($entries as $entry)
        <div class="mb-8 @if(!$loop->last) border-b pb-6 @endif">
            <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                <div>
                    <p><span class="text-gray-500">Reference:</span> <span class="font-medium">{{ $entry->reference_no }}</span></p>
                    <p><span class="text-gray-500">Date:</span> <span class="font-medium">{{ \Carbon\Carbon::parse($entry->transaction_date)->format('F j, Y') }}</span></p>
                </div>
                <div class="text-right">
                    <p><span class="text-gray-500">Status:</span>
                        <span class="font-medium {{ $entry->status === 'Posted' ? 'text-green-600' : '' }}">{{ $entry->status }}</span>
                    </p>
                </div>
            </div>
            <p class="text-sm text-gray-700 mb-3">{{ $entry->description }}</p>

            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-left text-gray-500">
                        <th class="py-2 px-3 font-medium">Account</th>
                        <th class="py-2 px-3 font-medium">Description</th>
                        <th class="py-2 px-3 font-medium text-right">Debit</th>
                        <th class="py-2 px-3 font-medium text-right">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalDebit = 0; $totalCredit = 0; @endphp
                    @foreach($entry->lines as $line)
                        @php
                            $totalDebit += $line->debit;
                            $totalCredit += $line->credit;
                        @endphp
                        <tr class="border-b last:border-0">
                            <td class="py-2 px-3">{{ $line->account->account_code ?? '' }} - {{ $line->account->account_name ?? '' }}</td>
                            <td class="py-2 px-3">{{ $line->description }}</td>
                            <td class="py-2 px-3 text-right">{{ $line->debit > 0 ? '₱' . number_format($line->debit, 2) : '' }}</td>
                            <td class="py-2 px-3 text-right">{{ $line->credit > 0 ? '₱' . number_format($line->credit, 2) : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t font-medium">
                        <td colspan="2" class="py-2 px-3 text-right">Totals</td>
                        <td class="py-2 px-3 text-right">₱{{ number_format($totalDebit, 2) }}</td>
                        <td class="py-2 px-3 text-right">₱{{ number_format($totalCredit, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endforeach
@endsection
