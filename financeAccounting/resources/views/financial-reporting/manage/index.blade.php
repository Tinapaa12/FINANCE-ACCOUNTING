@extends('layouts.app')

@section('title', 'Manage Data')
@section('page-title', 'Manage Data')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
    @endif

    <div class="inline-flex bg-white rounded-lg border p-1 flex-wrap">
        <a href="?tab=income" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ ($tab = request('tab', 'income')) === 'income' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">Income Statement</a>
        <a href="?tab=trial" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $tab === 'trial' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">Trial Balance</a>
        <a href="?tab=balance" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $tab === 'balance' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">Balance Sheet</a>
        <a href="?tab=cashflow" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $tab === 'cashflow' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">Cash Flow</a>
        <a href="?tab=budget" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $tab === 'budget' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">Budget vs Actual</a>
        <a href="?tab=tax" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $tab === 'tax' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">Tax Records</a>
    </div>

    @if($tab === 'income')
        <div class="bg-white rounded-lg border p-5">
            <h3 class="font-semibold mb-3">Create Report Period</h3>
            <form method="POST" action="{{ route('reports.manage.store-report') }}" class="flex gap-3 items-end flex-wrap">
                @csrf
                <div><label class="text-xs text-gray-500 block mb-1">Start</label><input type="date" name="report_period_start" required class="border rounded px-3 py-1.5 text-sm"></div>
                <div><label class="text-xs text-gray-500 block mb-1">End</label><input type="date" name="report_period_end" required class="border rounded px-3 py-1.5 text-sm"></div>
                <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Create</button>
            </form>
        </div>

        <div class="bg-white rounded-lg border p-5">
            <h3 class="font-semibold mb-3">Add Line</h3>
            <form method="POST" action="{{ route('reports.manage.store-income-line') }}" class="flex gap-3 items-end flex-wrap">
                @csrf
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Period</label>
                    <select name="income_statement_id" required class="border rounded px-3 py-1.5 text-sm">
                        @forelse($reports as $r)
                            <option value="{{ $r->incomeStatement?->income_statement_id }}">{{ $r->report_period_start->format('M Y') }}</option>
                        @empty
                            <option value="" disabled>Create a period first</option>
                        @endforelse
                    </select>
                </div>
                <div><label class="text-xs text-gray-500 block mb-1">Name</label><input type="text" name="line_name" required class="border rounded px-3 py-1.5 text-sm" placeholder="e.g. Sales revenue"></div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Type</label>
                    <select name="category" required class="border rounded px-3 py-1.5 text-sm">
                        <option value="revenue">Revenue</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
                <div><label class="text-xs text-gray-500 block mb-1">Amount (₱)</label><input type="number" step="0.01" name="amount" required class="border rounded px-3 py-1.5 text-sm"></div>
                <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Add</button>
            </form>
        </div>

    @elseif($tab === 'trial')
        <div class="bg-white rounded-lg border p-5">
            <h3 class="font-semibold mb-3">Add Trial Balance Entry</h3>
            <form method="POST" action="{{ route('reports.manage.store-trial') }}" class="flex gap-3 items-end flex-wrap">
                @csrf
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Period</label>
                    <select name="report_id" required class="border rounded px-3 py-1.5 text-sm">
                        @forelse($reports as $r)
                            <option value="{{ $r->report_id }}">{{ $r->report_period_start->format('M Y') }}</option>
                        @empty
                            <option value="" disabled>Create a period first</option>
                        @endforelse
                    </select>
                </div>
                <div><label class="text-xs text-gray-500 block mb-1">Account</label><input type="text" name="account_name" required class="border rounded px-3 py-1.5 text-sm" placeholder="e.g. Cash in bank"></div>
                <div><label class="text-xs text-gray-500 block mb-1">Debit (₱)</label><input type="number" step="0.01" name="debit_amount" class="border rounded px-3 py-1.5 text-sm"></div>
                <div><label class="text-xs text-gray-500 block mb-1">Credit (₱)</label><input type="number" step="0.01" name="credit_amount" class="border rounded px-3 py-1.5 text-sm"></div>
                <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Add</button>
            </form>
        </div>

    @elseif($tab === 'balance')
        <div class="bg-white rounded-lg border p-5">
            <h3 class="font-semibold mb-3">Add Balance Sheet Line</h3>
            <form method="POST" action="{{ route('reports.manage.store-balance') }}" class="flex gap-3 items-end flex-wrap">
                @csrf
                <div><label class="text-xs text-gray-500 block mb-1">Line Name</label><input type="text" name="line_name" required class="border rounded px-3 py-1.5 text-sm" placeholder="e.g. Cash on hand"></div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Section</label>
                    <select name="section" required class="border rounded px-3 py-1.5 text-sm">
                        <option value="Asset">Asset</option>
                        <option value="Liability">Liability</option>
                        <option value="Equity">Equity</option>
                    </select>
                </div>
                <div><label class="text-xs text-gray-500 block mb-1">Amount (₱)</label><input type="number" step="0.01" name="amount" required class="border rounded px-3 py-1.5 text-sm"></div>
                <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Add</button>
            </form>
        </div>

    @elseif($tab === 'cashflow')
        <div class="bg-white rounded-lg border p-5">
            <h3 class="font-semibold mb-3">Add Cash Flow Line</h3>
            <form method="POST" action="{{ route('reports.manage.store-cashflow') }}" class="flex gap-3 items-end flex-wrap">
                @csrf
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Activity</label>
                    <select name="activity_type" required class="border rounded px-3 py-1.5 text-sm">
                        <option value="Operating">Operating</option>
                        <option value="Investing">Investing</option>
                        <option value="Financing">Financing</option>
                    </select>
                </div>
                <div><label class="text-xs text-gray-500 block mb-1">Line Name</label><input type="text" name="line_name" required class="border rounded px-3 py-1.5 text-sm" placeholder="e.g. Cash received from customers"></div>
                <div><label class="text-xs text-gray-500 block mb-1">Amount (₱)</label><input type="number" step="0.01" name="amount" required class="border rounded px-3 py-1.5 text-sm"></div>
                <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Add</button>
            </form>
        </div>

    @elseif($tab === 'budget')
        <div class="bg-white rounded-lg border p-5">
            <h3 class="font-semibold mb-3">Add Budget vs Actual Entry</h3>
            <form method="POST" action="{{ route('reports.manage.store-budget') }}" class="flex gap-3 items-end flex-wrap">
                @csrf
                <div><label class="text-xs text-gray-500 block mb-1">Account</label><input type="text" name="account_name" required class="border rounded px-3 py-1.5 text-sm" placeholder="e.g. Sales revenue"></div>
                <div><label class="text-xs text-gray-500 block mb-1">Budget (₱)</label><input type="number" step="0.01" name="budget_amount" required class="border rounded px-3 py-1.5 text-sm"></div>
                <div><label class="text-xs text-gray-500 block mb-1">Actual (₱)</label><input type="number" step="0.01" name="actual_amount" required class="border rounded px-3 py-1.5 text-sm"></div>
                <div><label class="text-xs text-gray-500 block mb-1">Start</label><input type="date" name="report_period_start" required class="border rounded px-3 py-1.5 text-sm"></div>
                <div><label class="text-xs text-gray-500 block mb-1">End</label><input type="date" name="report_period_end" required class="border rounded px-3 py-1.5 text-sm"></div>
                <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Add</button>
            </form>
        </div>

    @elseif($tab === 'tax')
        <div class="bg-white rounded-lg border p-5">
            <h3 class="font-semibold mb-3">Add Tax Record</h3>
            <form method="POST" action="{{ route('reports.manage.store-tax') }}" class="flex gap-3 items-end flex-wrap">
                @csrf
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Type</label>
                    <select name="reference_type" required class="border rounded px-3 py-1.5 text-sm">
                        <option value="Customer Invoice">Customer Invoice</option>
                        <option value="Supplier Bill">Supplier Bill</option>
                    </select>
                </div>
                <div><label class="text-xs text-gray-500 block mb-1">Reference #</label><input type="number" name="reference_id" required class="border rounded px-3 py-1.5 text-sm"></div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Tax Type</label>
                    <select name="tax_type" required class="border rounded px-3 py-1.5 text-sm">
                        <option value="VAT">VAT</option>
                        <option value="EWT">EWT</option>
                    </select>
                </div>
                <div><label class="text-xs text-gray-500 block mb-1">Taxable (₱)</label><input type="number" step="0.01" name="taxable_amount" required class="border rounded px-3 py-1.5 text-sm"></div>
                <div><label class="text-xs text-gray-500 block mb-1">Rate (%)</label><input type="number" step="0.01" name="tax_rate" required class="border rounded px-3 py-1.5 text-sm"></div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Period</label>
                    <select name="tax_period" required class="border rounded px-3 py-1.5 text-sm">
                        @forelse($periods as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @empty
                            <option value="{{ now()->format('F Y') }}">{{ now()->format('F Y') }}</option>
                        @endforelse
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Status</label>
                    <select name="filing_status" required class="border rounded px-3 py-1.5 text-sm">
                        <option value="paid">Paid</option>
                        <option value="filed">Filed</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Add</button>
            </form>
        </div>
    @endif

    <p class="text-xs text-gray-400 text-center">Data added here will appear on the corresponding report pages.</p>
</div>
@endsection
