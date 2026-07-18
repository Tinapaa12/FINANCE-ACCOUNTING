{{-- resources/views/pdf/income.blade.php --}}
@extends('layouts.pdf')

@section('title', 'Income Statement PDF')
@section('pdf-title', 'Income Statement')

@section('pdf-content')
    <p class="text-sm text-gray-500 mb-6">{{ $report ? $report->report_period_start->format('M d, Y') . ' — ' . $report->report_period_end->format('M d, Y') : 'All periods' }}</p>

    <table class="w-full text-sm mb-8">
        <tr class="bg-green-50">
            <td class="py-2 px-3 font-semibold" colspan="2">Revenue Earned</td>
        </tr>
        @php $totalRevenue = 0; @endphp
        @foreach($revenue as $item)
            @php $totalRevenue += $item['amount']; @endphp
            <tr>
                <td class="py-1.5 px-3">{{ $item['label'] }}</td>
                <td class="py-1.5 px-3 text-right">₱{{ number_format($item['amount']) }}</td>
            </tr>
        @endforeach
        <tr class="font-semibold border-t">
            <td class="py-2 px-3">Total Revenue</td>
            <td class="py-2 px-3 text-right">₱{{ number_format($totalRevenue) }}</td>
        </tr>
    </table>

    <table class="w-full text-sm">
        <tr class="bg-red-50">
            <td class="py-2 px-3 font-semibold" colspan="2">Expenses</td>
        </tr>
        @php $totalExpenses = 0; @endphp
        @foreach($expenses as $item)
            @php $totalExpenses += $item['amount']; @endphp
            <tr>
                <td class="py-1.5 px-3">{{ $item['label'] }}</td>
                <td class="py-1.5 px-3 text-right">₱{{ number_format($item['amount']) }}</td>
            </tr>
        @endforeach
        <tr class="font-semibold border-t">
            <td class="py-2 px-3">Total Expenses</td>
            <td class="py-2 px-3 text-right">₱{{ number_format($totalExpenses) }}</td>
        </tr>
    </table>

    <div class="mt-6 pt-4 border-t flex justify-between font-bold text-base">
        <span>Net Income</span>
        <span>₱{{ number_format($totalRevenue - $totalExpenses) }}</span>
    </div>
@endsection
