@extends('layouts.app')

@section('title', 'Balance Sheet - Assets')

@section('page-title', 'Balance Sheet')

@section('content')
    <div class="flex flex-col lg:flex-row gap-6">
        <div class="flex-1">
            <div class="bg-white rounded-lg border p-5">
                <h2 class="font-semibold text-lg mb-4">Assets</h2>
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    @php $totalAssets = 0; @endphp
                    @foreach($assets as $item)
                        @php $totalAssets += $item['amount']; @endphp
                        <div class="flex justify-between text-sm text-blue-900 py-1">
                            <span>{{ $item['label'] }}</span>
                            <span>₱{{ number_format($item['amount']) }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between font-semibold text-blue-900 mt-3 pt-3 border-t border-blue-200">
                        <span>Total Assets</span>
                        <span>₱{{ number_format($totalAssets) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-80">
            <div class="bg-white rounded-lg border p-5">
                <h2 class="font-semibold text-lg mb-4">Liabilities & Equity</h2>

                <div class="bg-purple-50 rounded-lg p-4 mb-4">
                    <p class="font-semibold text-purple-900 mb-2">Liabilities</p>
                    @php $totalLiabilities = 0; @endphp
                    @foreach($liabilities as $item)
                        @php $totalLiabilities += $item['amount']; @endphp
                        <div class="flex justify-between text-sm text-purple-900 py-1">
                            <span>{{ $item['label'] }}</span>
                            <span>₱{{ number_format($item['amount']) }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between font-semibold text-purple-900 mt-3 pt-3 border-t border-purple-200">
                        <span>Total Liabilities</span>
                        <span>₱{{ number_format($totalLiabilities) }}</span>
                    </div>
                </div>

                <div class="bg-amber-50 rounded-lg p-4">
                    <p class="font-semibold text-amber-900 mb-2">Equity</p>
                    @php $totalEquity = 0; @endphp
                    @foreach($equity as $item)
                        @php $totalEquity += $item['amount']; @endphp
                        <div class="flex justify-between text-sm text-amber-900 py-1">
                            <span>{{ $item['label'] }}</span>
                            <span>₱{{ number_format($item['amount']) }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between font-semibold text-amber-900 mt-3 pt-3 border-t border-amber-200">
                        <span>Total Equity</span>
                        <span>₱{{ number_format($totalEquity) }}</span>
                    </div>
                </div>

                <div class="flex justify-between font-bold text-base pt-4 mt-4 border-t-2 border-slate-900">
                    <span>Liabilities + Equity</span>
                    <span>₱{{ number_format($totalLiabilities + $totalEquity) }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
