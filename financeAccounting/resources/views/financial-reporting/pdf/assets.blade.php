{{-- resources/views/pdf/assets.blade.php --}}
@extends('layouts.pdf')

@section('title', 'Balance Sheet PDF')
@section('pdf-title', 'Balance Sheet')

@section('pdf-content')
    <p class="text-sm text-gray-500 mb-6">Period: {{ $selectedPeriod ?? 'All periods' }}</p>

    <p class="font-semibold mb-2">Assets</p>
    <table class="w-full text-sm mb-6">
        @php $totalAssets = 0; @endphp
        @foreach($assets as $item)
            @php $totalAssets += $item['amount']; @endphp
            <tr>
                <td class="py-1.5 px-3">{{ $item['label'] }}</td>
                <td class="py-1.5 px-3 text-right">₱{{ number_format($item['amount']) }}</td>
            </tr>
        @endforeach
        <tr class="font-semibold border-t">
            <td class="py-2 px-3">Total Assets</td>
            <td class="py-2 px-3 text-right">₱{{ number_format($totalAssets) }}</td>
        </tr>
    </table>

    <p class="font-semibold mb-2">Liabilities</p>
    <table class="w-full text-sm mb-6">
        @php $totalLiabilities = 0; @endphp
        @foreach($liabilities as $item)
            @php $totalLiabilities += $item['amount']; @endphp
            <tr>
                <td class="py-1.5 px-3">{{ $item['label'] }}</td>
                <td class="py-1.5 px-3 text-right">₱{{ number_format($item['amount']) }}</td>
            </tr>
        @endforeach
        <tr class="font-semibold border-t">
            <td class="py-2 px-3">Total Liabilities</td>
            <td class="py-2 px-3 text-right">₱{{ number_format($totalLiabilities) }}</td>
        </tr>
    </table>

    <p class="font-semibold mb-2">Equity</p>
    <table class="w-full text-sm mb-6">
        @php $totalEquity = 0; @endphp
        @foreach($equity as $item)
            @php $totalEquity += $item['amount']; @endphp
            <tr>
                <td class="py-1.5 px-3">{{ $item['label'] }}</td>
                <td class="py-1.5 px-3 text-right">₱{{ number_format($item['amount']) }}</td>
            </tr>
        @endforeach
        <tr class="font-semibold border-t">
            <td class="py-2 px-3">Total Equity</td>
            <td class="py-2 px-3 text-right">₱{{ number_format($totalEquity) }}</td>
        </tr>
    </table>

    <div class="mt-6 pt-4 border-t flex justify-between font-bold text-base">
        <span>Liabilities + Equity</span>
        <span>₱{{ number_format($totalLiabilities + $totalEquity) }}</span>
    </div>
@endsection
