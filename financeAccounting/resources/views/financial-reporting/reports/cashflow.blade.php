{{-- resources/views/reports/cashflow.blade.php --}}
@extends('layouts.app')

@section('title', 'Cash Flow Statement')

@section('content')
    @if(!$hasData)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <p class="text-yellow-800 font-medium">No data yet.</p>
            <p class="text-yellow-600 text-sm mt-1">Add your first cash flow data from the <a href="{{ route('reports.manage') }}" class="underline">Manage Data</a> page.</p>
        </div>
    @else
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- LEFT: Activity breakdown --}}
        <div class="flex-1">
            <div class="bg-white rounded-lg border p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-lg">Cash Flow Statement</h2>
                    <div class="flex items-center gap-3">
                        <select class="border rounded px-3 py-1.5 text-sm" onchange="window.location.href='?report_id='+this.value">
                            @foreach($reports as $r)
                                <option value="{{ $r->report_id }}" @selected($r->report_id === $selectedReportId)>
                                    {{ $r->report_period_start->format('F Y') }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-sm text-gray-500">{{ $periodLabel }}</span>
                    </div>
                </div>

                {{-- Operating --}}
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <p class="font-semibold text-blue-900 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Operating Activities
                    </p>
                    @foreach($operating as $line)
                        <div class="flex justify-between text-sm text-blue-900 py-1">
                            <span>{{ $line['label'] }}</span>
                            <span class="{{ $line['amount'] < 0 ? 'text-red-500' : '' }}">
                                {{ $line['amount'] < 0 ? '-' : '' }}₱{{ number_format(abs($line['amount'])) }}
                            </span>
                        </div>
                    @endforeach
                    <div class="flex justify-between font-semibold text-blue-900 mt-3 pt-3 border-t border-blue-200">
                        <span>Net Cash from Operating</span>
                        <span>₱{{ number_format($totalOperating) }}</span>
                    </div>
                </div>

                {{-- Investing --}}
                <div class="bg-purple-50 rounded-lg p-4 mb-4">
                    <p class="font-semibold text-purple-900 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        Investing Activities
                    </p>
                    @foreach($investing as $line)
                        <div class="flex justify-between text-sm text-purple-900 py-1">
                            <span>{{ $line['label'] }}</span>
                            <span class="{{ $line['amount'] < 0 ? 'text-red-500' : '' }}">
                                {{ $line['amount'] < 0 ? '-' : '' }}₱{{ number_format(abs($line['amount'])) }}
                            </span>
                        </div>
                    @endforeach
                    <div class="flex justify-between font-semibold text-purple-900 mt-3 pt-3 border-t border-purple-200">
                        <span>Net Cash from Investing</span>
                        <span>₱{{ number_format($totalInvesting) }}</span>
                    </div>
                </div>

                {{-- Financing --}}
                <div class="bg-amber-50 rounded-lg p-4">
                    <p class="font-semibold text-amber-900 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v2m9-8a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Financing Activities
                    </p>
                    @foreach($financing as $line)
                        <div class="flex justify-between text-sm text-amber-900 py-1">
                            <span>{{ $line['label'] }}</span>
                            <span class="{{ $line['amount'] < 0 ? 'text-red-500' : '' }}">
                                {{ $line['amount'] < 0 ? '-' : '' }}₱{{ number_format(abs($line['amount'])) }}
                            </span>
                        </div>
                    @endforeach
                    <div class="flex justify-between font-semibold text-amber-900 mt-3 pt-3 border-t border-amber-200">
                        <span>Net Cash from Financing</span>
                        <span>₱{{ number_format($totalFinancing) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Cash summary --}}
        <div class="w-full lg:w-80">
            <div class="bg-white rounded-lg border p-5">
                <h2 class="font-semibold text-lg mb-4">Cash Summary</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-gray-500">
                        <span>Beginning Cash Balance</span>
                        <span class="text-gray-700">₱{{ number_format($beginningCash) }}</span>
                    </div>

                    <div class="flex justify-between pt-3 border-t">
                        <span class="text-blue-700">Operating</span>
                        <span class="{{ $totalOperating < 0 ? 'text-red-500' : 'text-blue-700' }}">
                            {{ $totalOperating < 0 ? '-' : '+' }}₱{{ number_format(abs($totalOperating)) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-purple-700">Investing</span>
                        <span class="{{ $totalInvesting < 0 ? 'text-red-500' : 'text-purple-700' }}">
                            {{ $totalInvesting < 0 ? '-' : '+' }}₱{{ number_format(abs($totalInvesting)) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-amber-700">Financing</span>
                        <span class="{{ $totalFinancing < 0 ? 'text-red-500' : 'text-amber-700' }}">
                            {{ $totalFinancing < 0 ? '-' : '+' }}₱{{ number_format(abs($totalFinancing)) }}
                        </span>
                    </div>

                    <div class="flex justify-between font-semibold pt-3 border-t">
                        <span>Net Change in Cash</span>
                        <span class="{{ $netCashFlow < 0 ? 'text-red-500' : 'text-green-600' }}">
                            {{ $netCashFlow < 0 ? '-' : '+' }}₱{{ number_format(abs($netCashFlow)) }}
                        </span>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t-2 border-slate-900">
                    <p class="text-xs text-gray-500 mb-1">Ending Cash Balance</p>
                    <p class="text-2xl font-bold text-slate-900">₱{{ number_format($endingCash) }}</p>
                </div>
            </div>
        </div>

    </div>
    @endif
@endsection