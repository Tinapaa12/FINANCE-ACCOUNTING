@extends('layouts.app')

@section('title', 'Budget vs Actual')

@section('page-title', 'Budget vs Actual')

@section('content')
    <div class="bg-white rounded-lg border p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-lg">
                Budget vs Actual
                <span class="relative group inline-block ml-1.5">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold cursor-help">!</span>
                    <div class="absolute left-0 top-6 z-50 hidden group-hover:block w-80 bg-gray-900 text-white text-xs rounded-lg shadow-xl p-4 leading-relaxed">
                        <p class="font-semibold mb-1 text-blue-300">How Actual is computed:</p>
                        <p class="mb-1">For each account, the system queries <span class="text-yellow-300">Posted</span> journal entries in the selected period.</p>
                        <p class="mb-1">
                            If account normal balance is <span class="text-green-300">Credit</span> (Revenue):
                            <br><span class="text-green-300 ml-2">Actual = max(Total Credits − Total Debits, 0)</span>
                        </p>
                        <p class="mb-1">
                            If account normal balance is <span class="text-orange-300">Debit</span> (Expense):
                            <br><span class="text-orange-300 ml-2">Actual = max(Total Debits − Total Credits, 0)</span>
                        </p>
                        <p class="mb-1 mt-2 border-t border-gray-600 pt-2">
                            <span class="text-blue-300">Variance</span> = Actual − Budget
                        </p>
                        <p>
                            <span class="text-blue-300">Status:</span> over (variance > 0) | under (variance < 0) | on_budget (variance = 0)
                        </p>
                    </div>
                </span>
            </h2>
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
