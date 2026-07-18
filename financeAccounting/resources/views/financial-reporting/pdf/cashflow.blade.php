{{-- resources/views/pdf/cashflow.blade.php --}}
@extends('layouts.pdf')

@section('title', 'Cash Flow Statement PDF')
@section('pdf-title', 'Cash Flow Statement')

@section('pdf-content')
    <p class="text-sm text-gray-500 mb-6">{{ $periodLabel }}</p>

    <table class="w-full text-sm mb-6">
        <tr class="bg-green-50">
            <td class="py-2 px-3 font-semibold text-green-900" colspan="2">Cash In</td>
        </tr>
        @foreach($cashInLines as $line)
            <tr>
                <td class="py-1.5 px-3">{{ $line['label'] }}</td>
                <td class="py-1.5 px-3 text-right">₱{{ number_format($line['amount']) }}</td>
            </tr>
        @endforeach
        <tr class="font-semibold border-t">
            <td class="py-2 px-3">Total Cash In</td>
            <td class="py-2 px-3 text-right">₱{{ number_format($totalCashIn) }}</td>
        </tr>
    </table>

    <table class="w-full text-sm mb-6">
        <tr class="bg-red-50">
            <td class="py-2 px-3 font-semibold text-red-900" colspan="2">Cash Out</td>
        </tr>
        @foreach($cashOutLines as $line)
            <tr>
                <td class="py-1.5 px-3">{{ $line['label'] }}</td>
                <td class="py-1.5 px-3 text-right">₱{{ number_format($line['amount']) }}</td>
            </tr>
        @endforeach
        <tr class="font-semibold border-t">
            <td class="py-2 px-3">Total Cash Out</td>
            <td class="py-2 px-3 text-right">₱{{ number_format($totalCashOut) }}</td>
        </tr>
    </table>

    <div class="mt-6 pt-4 border-t flex justify-between font-bold text-base">
        <span>Net Cash Flow</span>
        <span class="{{ $netCashFlow < 0 ? 'text-red-500' : 'text-green-600' }}">
            {{ $netCashFlow < 0 ? '-' : '' }}₱{{ number_format(abs($netCashFlow)) }}
        </span>
    </div>
    <div class="flex justify-between text-sm text-gray-500 mt-2">
        <span>Beginning Cash Balance</span>
        <span>₱{{ number_format($beginningCash) }}</span>
    </div>
    <div class="flex justify-between font-semibold mt-1">
        <span>Ending Cash Balance</span>
        <span>₱{{ number_format($endingCash) }}</span>
    </div>
@endsection