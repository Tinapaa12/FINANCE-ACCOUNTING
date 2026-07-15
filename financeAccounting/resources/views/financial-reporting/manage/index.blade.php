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
        <a href="?tab=balance" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $tab === 'balance' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">Balance Sheet</a>
        <a href="?tab=cashflow" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $tab === 'cashflow' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">Cash Flow</a>
        <a href="?tab=budget" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $tab === 'budget' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">Budget vs Actual</a>
        <a href="?tab=tax" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $tab === 'tax' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">Tax Records</a>
    </div>

    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800">
        <strong>Quick-entry mode.</strong> Each form creates a balanced journal entry in the General Ledger so data appears on reports immediately.
    </div>

    @if($tab === 'income')
        <div class="bg-white rounded-lg border p-5">
            <h3 class="font-semibold mb-3">Add Revenue or Expense</h3>
            <p class="text-xs text-gray-500 mb-3">Creates a journal entry with a Cash offset. Reports will reflect this data immediately.</p>
            <form method="POST" action="{{ route('reports.manage.store-income') }}" class="flex gap-3 items-end flex-wrap">
                @csrf
                <div><label class="text-xs text-gray-500 block mb-1">Account Name</label><input type="text" name="account_name" required class="border rounded px-3 py-1.5 text-sm" placeholder="e.g. Service Revenue"></div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Type</label>
                    <select name="category" required class="border rounded px-3 py-1.5 text-sm">
                        <option value="Revenue">Revenue</option>
                        <option value="Expense">Expense</option>
                    </select>
                </div>
                <div><label class="text-xs text-gray-500 block mb-1">Amount (₱)</label><input type="number" step="0.01" name="amount" required class="border rounded px-3 py-1.5 text-sm"></div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Period</label>
                    <select name="period" required class="border rounded px-3 py-1.5 text-sm">
                        @foreach($reportPeriods as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Add</button>
            </form>
        </div>

    @elseif($tab === 'balance')
        <div class="bg-white rounded-lg border p-5">
            <h3 class="font-semibold mb-3">Add Balance Sheet Line</h3>
            <p class="text-xs text-gray-500 mb-3">Creates a journal entry with a default offset account. The account is auto-created if it doesn't exist.</p>
            <form method="POST" action="{{ route('reports.manage.store-balance') }}" class="flex gap-3 items-end flex-wrap">
                @csrf
                <div><label class="text-xs text-gray-500 block mb-1">Account Name</label><input type="text" name="account_name" required class="border rounded px-3 py-1.5 text-sm" placeholder="e.g. Building"></div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Section</label>
                    <select name="section" required class="border rounded px-3 py-1.5 text-sm">
                        <option value="Asset">Asset</option>
                        <option value="Liability">Liability</option>
                        <option value="Equity">Equity</option>
                    </select>
                </div>
                <div><label class="text-xs text-gray-500 block mb-1">Amount (₱)</label><input type="number" step="0.01" name="amount" required class="border rounded px-3 py-1.5 text-sm"></div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Period</label>
                    <select name="period" required class="border rounded px-3 py-1.5 text-sm">
                        @foreach($reportPeriods as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Add</button>
            </form>
        </div>

    @elseif($tab === 'cashflow')
        <div class="bg-white rounded-lg border p-5">
            <h3 class="font-semibold mb-3">Record Cash Movement</h3>
            <p class="text-xs text-gray-500 mb-3">Creates a journal entry affecting a Cash account and the specified account.</p>
            <form method="POST" action="{{ route('reports.manage.store-cashflow') }}" class="flex gap-3 items-end flex-wrap">
                @csrf
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Type</label>
                    <select name="flow_type" required class="border rounded px-3 py-1.5 text-sm">
                        <option value="Cash In">Cash In (received)</option>
                        <option value="Cash Out">Cash Out (paid)</option>
                    </select>
                </div>
                <div><label class="text-xs text-gray-500 block mb-1">Account</label><input type="text" name="account_name" required class="border rounded px-3 py-1.5 text-sm" placeholder="e.g. Customer Payment"></div>
                <div><label class="text-xs text-gray-500 block mb-1">Amount (₱)</label><input type="number" step="0.01" name="amount" required class="border rounded px-3 py-1.5 text-sm"></div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Period</label>
                    <select name="period" required class="border rounded px-3 py-1.5 text-sm">
                        @foreach($reportPeriods as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Add</button>
            </form>
        </div>

    @elseif($tab === 'budget')
        <div class="bg-white rounded-lg border p-5">
            <h3 class="font-semibold mb-3">Add Budget Target</h3>
            <p class="text-xs text-gray-500 mb-3">Actual amounts are auto-computed from posted journal entries in the General Ledger.</p>
            <form method="POST" action="{{ route('reports.manage.store-budget') }}" class="flex gap-3 items-end flex-wrap">
                @csrf
                <div><label class="text-xs text-gray-500 block mb-1">Account</label><input type="text" name="account_name" required class="border rounded px-3 py-1.5 text-sm" placeholder="e.g. Sales revenue"></div>
                <div><label class="text-xs text-gray-500 block mb-1">Budget (₱)</label><input type="number" step="0.01" name="budget_amount" required class="border rounded px-3 py-1.5 text-sm"></div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Period</label>
                    <select name="tax_period" required class="border rounded px-3 py-1.5 text-sm">
                        @foreach($reportPeriods as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Add</button>
            </form>
        </div>

    @elseif($tab === 'tax')
        <div class="bg-white rounded-lg border p-5">
            <h3 class="font-semibold mb-3">Filing Status Override</h3>
            <p class="text-xs text-gray-500 mb-3">Tax amounts are auto-computed from your General Ledger (VAT / tax accounts in journal entries). Use this form to mark a reference as Filed or Paid.</p>
            <form method="POST" action="{{ route('reports.manage.store-tax') }}" class="flex gap-3 items-end flex-wrap">
                @csrf
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Reference Type</label>
                    <select name="reference_type" required class="border rounded px-3 py-1.5 text-sm">
                        <option value="Journal Entry">Journal Entry</option>
                        <option value="Customer Invoice">Customer Invoice</option>
                        <option value="Supplier Bill">Supplier Bill</option>
                    </select>
                </div>
                <div><label class="text-xs text-gray-500 block mb-1">Reference #</label><input type="text" name="reference_id" required class="border rounded px-3 py-1.5 text-sm" placeholder="e.g. JE-2024-00130"></div>
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
                        @foreach($reportPeriods as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
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
                <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Override</button>
            </form>
        </div>
    @endif
</div>
@endsection
