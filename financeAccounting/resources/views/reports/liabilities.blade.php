@extends('layouts.app')

@section('title', 'Budget vs Actual')
@section('page-title', 'Budget vs Actual')

@section('content')
    <div class="bg-white rounded-lg border p-5">
        <h2 class="font-semibold text-lg mb-4">Budget vs Actual - {{ $reportDate }}</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-2 font-medium">Account</th>
                    <th class="py-2 font-medium">Budget</th>
                    <th class="py-2 font-medium">Actual</th>
                    <th class="py-2 font-medium">Variance</th>
                    <th class="py-2 font-medium">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($budgetVsActual as $row)
                    @php $variance = $row['actual'] - $row['budget']; @endphp
                    <tr class="border-b last:border-0">
                        <td class="py-2.5">{{ $row['account'] }}</td>
                        <td class="py-2.5">₱{{ number_format($row['budget']) }}</td>
                        <td class="py-2.5">₱{{ number_format($row['actual']) }}</td>
                        <td class="py-2.5 {{ $variance > 0 ? 'text-red-500' : ($variance < 0 ? 'text-green-600' : 'text-gray-500') }}">
                            {{ $variance > 0 ? '+' : '' }}₱{{ number_format($variance) }}
                        </td>
                        <td class="py-2.5">
                            @if($row['status'] === 'over')
                                <span class="text-red-500">Over Budget ⚠️</span>
                            @elseif($row['status'] === 'slightly_over')
                                <span class="text-orange-500">Slightly Over</span>
                            @elseif($row['status'] === 'on_budget')
                                <span class="text-green-600">On Budget ✓</span>
                            @else
                                <span class="text-green-600">Under Budget ✓</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
