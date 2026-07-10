{{-- resources/views/reports/cash-flow.blade.php --}}
@extends('layouts.app')

@section('title', 'Cash Flow Statement')

@section('content')
    <div class="bg-white rounded-lg border p-5 max-w-3xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-semibold text-lg">Cash Flow Statement</h2>
                <p class="text-sm text-gray-500">{{ $periodLabel }}</p>
            </div>
        </div>

        {{-- Operating --}}
        <div class="mb-6">
            <p class="font-semibold text-gray-700 mb-2">Operating Activities</p>
            @foreach($operating as $line)
                <div class="flex justify-between text-sm py-1.5 border-b last:border-0">
                    <span>{{ $line['label'] }}</span>
                    <span class="{{ $line['amount'] < 0 ? 'text-red-500' : '' }}">
                        {{ $line['amount'] < 0 ? '-' : '' }}₱{{ number_format(abs($line['amount'])) }}
                    </span>
                </div>
            @endforeach
            <div class="flex justify-between text-sm font-semibold pt-2 mt-1 border-t">
                <span>Net Cash from Operating</span>
                <span>₱{{ number_format($totalOperating) }}</span>
            </div>
        </div>

        {{-- Investing --}}
        <div class="mb-6">
            <p class="font-semibold text-gray-700 mb-2">Investing Activities</p>
            @foreach($investing as $line)
                <div class="flex justify-between text-sm py-1.5 border-b last:border-0">
                    <span>{{ $line['label'] }}</span>
                    <span class="{{ $line['amount'] < 0 ? 'text-red-500' : '' }}">
                        {{ $line['amount'] < 0 ? '-' : '' }}₱{{ number_format(abs($line['amount'])) }}
                    </span>
                </div>
            @endforeach
            <div class="flex justify-between text-sm font-semibold pt-2 mt-1 border-t">
                <span>Net Cash from Investing</span>
                <span>₱{{ number_format($totalInvesting) }}</span>
            </div>
        </div>

        {{-- Financing --}}
        <div class="mb-6">
            <p class="font-semibold text-gray-700 mb-2">Financing Activities</p>
            @foreach($financing as $line)
                <div class="flex justify-between text-sm py-1.5 border-b last:border-0">
                    <span>{{ $line['label'] }}</span>
                    <span class="{{ $line['amount'] < 0 ? 'text-red-500' : '' }}">
                        {{ $line['amount'] < 0 ? '-' : '' }}₱{{ number_format(abs($line['amount'])) }}
                    </span>
                </div>
            @endforeach
            <div class="flex justify-between text-sm font-semibold pt-2 mt-1 border-t">
                <span>Net Cash from Financing</span>
                <span>₱{{ number_format($totalFinancing) }}</span>
            </div>
        </div>

        {{-- Net change --}}
        <div class="flex justify-between font-bold text-base pt-4 border-t-2">
            <span>Net Change in Cash</span>
            <span class="{{ $netCashFlow < 0 ? 'text-red-500' : 'text-green-600' }}">
                {{ $netCashFlow < 0 ? '-' : '' }}₱{{ number_format(abs($netCashFlow)) }}
            </span>
        </div>

        <div class="flex justify-between text-sm text-gray-500 mt-2">
            <span>Beginning Cash Balance</span>
            <span>₱{{ number_format($beginningCash) }}</span>
        </div>
        <div class="flex justify-between font-semibold text-base mt-1">
            <span>Ending Cash Balance</span>
            <span>₱{{ number_format($endingCash) }}</span>
        </div>
    </div>
@endsection
