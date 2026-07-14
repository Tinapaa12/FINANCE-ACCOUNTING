@extends('layouts.app')

@section('title', 'Cash Flow Statement')

@section('content')
    @if(!$hasData)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <p class="text-yellow-800 font-medium">No data yet.</p>
            <p class="text-yellow-600 text-sm mt-1">Add journal entries and financial reports from the <a href="{{ route('reports.manage') }}" class="underline">Manage Data</a> page.</p>
        </div>
    @else
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- LEFT: Cash In / Cash Out breakdown --}}
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

                {{-- Cash In --}}
                <div class="bg-green-50 rounded-lg p-4 mb-4">
                    <p class="font-semibold text-green-900 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        Cash In
                    </p>
                    @forelse($cashInLines as $line)
                        <div class="flex justify-between text-sm text-green-900 py-1">
                            <span>{{ $line['label'] }}</span>
                            <span>₱{{ number_format($line['amount']) }}</span>
                        </div>
                    @empty
                        <div class="text-sm text-green-600 italic">No cash inflows this period.</div>
                    @endforelse
                    <div class="flex justify-between font-semibold text-green-900 mt-3 pt-3 border-t border-green-200">
                        <span>Total Cash In</span>
                        <span>₱{{ number_format($totalCashIn) }}</span>
                    </div>
                </div>

                {{-- Cash Out --}}
                <div class="bg-red-50 rounded-lg p-4">
                    <p class="font-semibold text-red-900 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Cash Out
                    </p>
                    @forelse($cashOutLines as $line)
                        <div class="flex justify-between text-sm text-red-900 py-1">
                            <span>{{ $line['label'] }}</span>
                            <span>₱{{ number_format($line['amount']) }}</span>
                        </div>
                    @empty
                        <div class="text-sm text-red-600 italic">No cash outflows this period.</div>
                    @endforelse
                    <div class="flex justify-between font-semibold text-red-900 mt-3 pt-3 border-t border-red-200">
                        <span>Total Cash Out</span>
                        <span>₱{{ number_format($totalCashOut) }}</span>
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

                    <div class="flex justify-between pt-3 border-t text-green-700">
                        <span>Cash In</span>
                        <span>+₱{{ number_format($totalCashIn) }}</span>
                    </div>
                    <div class="flex justify-between text-red-600">
                        <span>Cash Out</span>
                        <span>-₱{{ number_format($totalCashOut) }}</span>
                    </div>

                    <div class="flex justify-between font-semibold pt-3 border-t">
                        <span>Net Cash Flow</span>
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
