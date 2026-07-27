@extends('layouts.app')

@section('title', 'Balance Sheet')

@section('page-title', 'Balance Sheet')

@section('content')
    <div class="bg-white rounded-lg border p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-lg">
                Balance Sheet
                <span class="relative group inline-block ml-1.5">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold cursor-help">!</span>
                    <div class="absolute left-0 top-6 z-50 hidden group-hover:block w-80 bg-gray-900 text-white text-xs rounded-lg shadow-xl p-4 leading-relaxed">
                        <p class="font-semibold mb-1.5 text-blue-300">How Balance Sheet is computed:</p>
                        <p class="mb-1">Queries <span class="text-yellow-300">Posted</span> journal entries for Asset, Liability & Equity accounts.</p>
                        <p class="mb-1">
                            <span class="text-blue-300">Assets</span> (normal balance = Debit):
                            <br><span class="text-blue-300 ml-2">Balance = SUM(Debit) − SUM(Credit)</span>
                        </p>
                        <p class="mb-1">
                            <span class="text-purple-300">Liabilities</span> (normal balance = Credit):
                            <br><span class="text-purple-300 ml-2">Balance = SUM(Credit) − SUM(Debit)</span>
                        </p>
                        <p class="mb-1">
                            <span class="text-amber-300">Equity</span> (normal balance = Credit):
                            <br><span class="text-amber-300 ml-2">Balance = SUM(Credit) − SUM(Debit)</span>
                        </p>
                        <p class="mb-1">
                            <span class="text-green-300">Retained Earnings</span> = Net Income (Revenue − Expenses)
                            <br><span class="text-green-300 ml-2">added to Equity section</span>
                        </p>
                        <p class="mt-1.5 border-t border-gray-600 pt-1.5">
                            <span class="text-blue-300">Accounting Equation:</span>
                            <br><span class="ml-2">Assets = Liabilities + Equity</span>
                        </p>
                    </div>
                </span>
            </h2>
            <select class="border rounded px-3 py-1.5 text-sm" onchange="window.location.href='?period='+this.value">
                @foreach($periods as $p)
                    <option value="{{ $p }}" @selected($p === $selectedPeriod)>{{ $p }}</option>
                @endforeach
            </select>
        </div>

        @if(!$hasData)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <p class="text-yellow-800 font-medium">No data yet.</p>
                <p class="text-yellow-600 text-sm mt-1">Add journal entries with Asset, Liability, and Equity accounts first.</p>
            </div>
        @else
        <div class="flex flex-col lg:flex-row gap-6">
            <div class="flex-1">
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <p class="font-semibold text-blue-900 mb-2">Assets</p>
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

            <div class="flex-1">
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
        @endif
    </div>
@endsection