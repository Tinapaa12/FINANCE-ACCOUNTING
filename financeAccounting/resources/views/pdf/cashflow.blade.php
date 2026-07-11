{{-- resources/views/pdf/cashflow.blade.php --}}
@extends('layouts.pdf')

@section('title', 'Cash Flow Statement PDF')
@section('pdf-title', 'Cash Flow Statement')

@section('pdf-content')
    <p class="text-sm text-gray-500 mb-6">{{ $periodLabel }}</p>

    @php
        $sections = [
            ['label' => 'Operating Activities', 'lines' => $operating, 'bg' => 'bg-blue-50', 'text' => 'text-blue-900'],
            ['label' => 'Investing Activities', 'lines' => $investing, 'bg' => 'bg-purple-50', 'text' => 'text-purple-900'],
            ['label' => 'Financing Activities', 'lines' => $financing, 'bg' => 'bg-amber-50', 'text' => 'text-amber-900'],
        ];
    @endphp

    @foreach($sections as $section)
        <table class="w-full text-sm mb-6">
            <tr class="{{ $section['bg'] }}">
                <td class="py-2 px-3 font-semibold {{ $section['text'] }}" colspan="2">{{ $section['label'] }}</td>
            </tr>
            @foreach($section['lines'] as $line)
                <tr>
                    <td class="py-1.5 px-3">{{ $line['label'] }}</td>
                    <td class="py-1.5 px-3 text-right {{ $line['amount'] < 0 ? 'text-red-500' : '' }}">
                        {{ $line['amount'] < 0 ? '-' : '' }}₱{{ number_format(abs($line['amount'])) }}
                    </td>
                </tr>
            @endforeach
        </table>
    @endforeach

    <div class="mt-6 pt-4 border-t flex justify-between font-bold text-base">
        <span>Net Change in Cash</span>
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