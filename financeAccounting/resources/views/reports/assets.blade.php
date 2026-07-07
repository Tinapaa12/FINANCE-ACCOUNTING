{{-- resources/views/reports/assets.blade.php --}}
@extends('layouts.app')

@section('title', 'Balance Sheet - Assets')

@section('content')
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- LEFT: Assets --}}
        <div class="flex-1">
            <div class="bg-white rounded-lg border p-5">
                <h2 class="font-semibold text-lg mb-4">Assets</h2>

                @php $totalAssets = 0; @endphp
                @foreach($assets as $item)
                    @php $totalAssets += $item['amount']; @endphp
                    <div class="flex justify-between text-sm py-1.5">
                        <span>{{ $item['label'] }}</span>
                        <span>₱{{ number_format($item['amount']) }}</span>
                    </div>
                @endforeach

                <div class="flex justify-between font-semibold mt-3 pt-3 border-t">
                    <span>Total Asset</span>
                    <span class="text-green-600">₱{{ number_format($totalAssets) }}</span>
                </div>
            </div>
        </div>

        {{-- RIGHT: Liabilities & Equity --}}
        <div class="flex-1">
            <div class="bg-white rounded-lg border p-5">
                <h2 class="font-semibold text-lg mb-4">Liabilities & Equity</h2>

                <p class="text-gray-500 text-sm mb-1">Liabilities</p>
                @php $totalLiabilities = 0; @endphp
                @foreach($liabilities as $item)
                    @php $totalLiabilities += $item['amount']; @endphp
                    <div class="flex justify-between text-sm py-1.5">
                        <span>{{ $item['label'] }}</span>
                        <span>₱{{ number_format($item['amount']) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between font-semibold mt-2 pt-2 border-t">
                    <span>Total liabilities</span>
                    <span class="text-red-500">₱{{ number_format($totalLiabilities) }}</span>
                </div>

                <p class="text-gray-500 text-sm mt-5 mb-1">Equity</p>
                @php $totalEquity = 0; @endphp
                @foreach($equity as $item)
                    @php $totalEquity += $item['amount']; @endphp
                    <div class="flex justify-between text-sm py-1.5">
                        <span>{{ $item['label'] }}</span>
                        <span>₱{{ number_format($item['amount']) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between font-semibold mt-2 pt-2 border-t">
                    <span>Total equity</span>
                    <span class="text-green-600">₱{{ number_format($totalEquity) }}</span>
                </div>

                <div class="flex justify-between font-semibold mt-5 pt-3 border-t">
                    <span>LIAB + EQUITY</span>
                    <span class="text-green-600">₱{{ number_format($totalLiabilities + $totalEquity) }}</span>
                </div>

                <p class="text-center text-xs text-gray-400 mt-6">LIAB + EQUITY = ASSETS</p>
            </div>
        </div>

    </div>
@endsection
