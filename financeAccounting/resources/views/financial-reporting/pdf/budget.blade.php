{{-- resources/views/pdf/liabilities.blade.php --}}
@extends('layouts.pdf')

@section('title', 'Budget vs Actual PDF')
@section('pdf-title', 'Budget vs Actual')

@section('pdf-content')
    <p class="text-sm text-gray-500 mb-6">{{ $selectedPeriod ?? 'All periods' }}</p>

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b text-left text-gray-500">
                <th class="py-2 px-3 font-medium">Account</th>
                <th class="py-2 px-3 font-medium text-right">Budget</th>
                <th class="py-2 px-3 font-medium text-right">Actual</th>
                <th class="py-2 px-3 font-medium text-right">Variance</th>
                <th class="py-2 px-3 font-medium">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($budgetVsActual as $row)
                @php $variance = $row['actual'] - $row['budget']; @endphp
                <tr class="border-b last:border-0">
                    <td class="py-2 px-3">{{ $row['account'] }}</td>
                    <td class="py-2 px-3 text-right">₱{{ number_format($row['budget']) }}</td>
                    <td class="py-2 px-3 text-right">₱{{ number_format($row['actual']) }}</td>
                    <td class="py-2 px-3 text-right">{{ $variance > 0 ? '+' : '' }}₱{{ number_format($variance) }}</td>
                    <td class="py-2 px-3">{{ ucwords(str_replace('_', ' ', $row['status'])) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
