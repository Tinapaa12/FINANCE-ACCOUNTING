@extends('layouts.app')

@section('title', 'Budget vs Actual')

@section('page-title', 'Budget vs Actual')

@section('content')
    <div class="bg-white rounded-lg border p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-lg">Budget vs Actual</h2>
            <div>
                <span class="text-sm text-gray-500 mr-3">{{ $selectedPeriod ?? 'All periods' }}</span>
                <select class="border rounded px-3 py-1.5 text-sm" onchange="window.location.href='?period='+this.value">
                    @foreach($periods as $p)
                        <option value="{{ $p }}" @selected($p === $selectedPeriod)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if(empty($budgetVsActual))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <p class="text-yellow-800 font-medium">No budget targets yet.</p>
                <p class="text-yellow-600 text-sm mt-1">Add budget targets via Manage Data → Budget vs Actual tab. Actual amounts are auto-computed from posted journal entries.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-left text-gray-500">
                        <th class="py-3 px-4 font-medium">Account</th>
                        <th class="py-3 px-4 font-medium text-right">Budget</th>
                        <th class="py-3 px-4 font-medium text-right">Actual</th>
                        <th class="py-3 px-4 font-medium text-right">Variance</th>
                        <th class="py-3 px-4 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($budgetVsActual as $row)
                        @php $variance = $row['actual'] - $row['budget']; @endphp
                        <tr class="border-b last:border-0 hover:bg-gray-50">
                            <td class="py-3 px-4">{{ $row['account'] }}</td>
                            <td class="py-3 px-4 text-right">₱{{ number_format($row['budget']) }}</td>
                            <td class="py-3 px-4 text-right">₱{{ number_format($row['actual']) }}</td>
                            <td class="py-3 px-4 text-right {{ $variance > 0 ? 'text-red-500' : 'text-green-600' }}">
                                {{ $variance > 0 ? '+' : '' }}₱{{ number_format($variance) }}
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $statusClasses = [
                                        'over' => 'bg-red-100 text-red-800',
                                        'slightly_over' => 'bg-yellow-100 text-yellow-800',
                                        'under' => 'bg-green-100 text-green-800',
                                        'on_budget' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $class = $statusClasses[$row['status']] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $class }}">
                                    {{ ucwords(str_replace('_', ' ', $row['status'])) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
@endsection
