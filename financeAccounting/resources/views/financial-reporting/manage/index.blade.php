@extends('layouts.app')

@section('title', 'Manage Data')
@section('page-title', 'Manage Data')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
    @endif

    <div class="inline-flex bg-white rounded-lg border p-1 flex-wrap">
        <a href="?tab=budget" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ ($tab = request('tab', 'budget')) === 'budget' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">Budget vs Actual</a>
        <a href="?tab=tax" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $tab === 'tax' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">Tax Records</a>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
        All financial reports are computed directly from the General Ledger, Accounts Receivable, and Accounts Payable modules. Use this page only to enter budgets and manage tax filing statuses.
    </div>

    @if($tab === 'budget')
        <div class="bg-white rounded-lg border p-5">
            <h3 class="font-semibold mb-3">Add Budget Target</h3>
            <p class="text-xs text-gray-500 mb-3">Select a Chart of Accounts account and set budget and actual amounts.</p>
            <form method="POST" action="{{ route('reports.manage.store-budget') }}" class="flex gap-3 items-end flex-wrap">
                @csrf
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Account</label>
                    <select name="account_id" required class="border rounded px-3 py-1.5 text-sm min-w-[200px]">
                        <option value="">Select account</option>
                        @foreach($accounts as $a)
                            <option value="{{ $a->account_id }}">{{ $a->account_code }} - {{ $a->account_name }} ({{ $a->type }})</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="text-xs text-gray-500 block mb-1">Budget (₱)</label><input type="number" step="0.01" name="budget_amount" required class="border rounded px-3 py-1.5 text-sm"></div>
                <div><label class="text-xs text-gray-500 block mb-1">Actual (₱)</label><input type="number" step="0.01" name="actual_amount" class="border rounded px-3 py-1.5 text-sm"></div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Period</label>
                    <select name="tax_period" required class="border rounded px-3 py-1.5 text-sm">
                        @forelse($reportPeriods as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @empty
                            <option value="{{ now()->format('F Y') }}">{{ now()->format('F Y') }}</option>
                        @endforelse
                    </select>
                </div>
                <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Add</button>
            </form>
        </div>

        @if(($budgetRows = \App\Models\FinancialReporting\BudgetVsActual::orderByDesc('report_period_start')->get())->isNotEmpty())
            <div class="bg-white rounded-lg border overflow-hidden">
                <table class="w-full text-sm">
                    <thead><tr class="bg-gray-50 text-left border-b"><th class="px-4 py-2 font-semibold">Account</th><th class="px-4 py-2 font-semibold text-right">Budget</th><th class="px-4 py-2 font-semibold text-right">Actual</th><th class="px-4 py-2 font-semibold text-right">Variance</th><th class="px-4 py-2 font-semibold">Status</th><th class="px-4 py-2 font-semibold text-right">Period</th><th class="px-4 py-2"></th></tr></thead>
                    <tbody>
                        @foreach($budgetRows as $b)
                            @php $variance = $b->actual_amount - $b->budget_amount; @endphp
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $b->account_name }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($b->budget_amount, 2) }}</td>
                                <td class="px-4 py-2 text-right">
                                    <form method="POST" action="{{ route('reports.manage.update-budget-actual', $b) }}" class="inline-flex items-center gap-1">
                                        @csrf @method('PUT')
                                        <input type="number" step="0.01" name="actual_amount" value="{{ $b->actual_amount }}" class="w-24 border rounded px-2 py-1 text-right text-sm">
                                        <button class="text-blue-600 hover:text-blue-800 text-xs font-medium">Save</button>
                                    </form>
                                </td>
                                <td class="px-4 py-2 text-right {{ $variance > 0 ? 'text-red-500' : 'text-green-600' }}">{{ number_format($variance, 2) }}</td>
                                <td class="px-4 py-2">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $variance > 0 ? 'bg-red-100 text-red-800' : ($variance < 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ $variance > 0 ? 'Over Budget' : ($variance < 0 ? 'Under Budget' : 'On Budget') }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right">{{ \Carbon\Carbon::parse($b->report_period_start)->format('F Y') }}</td>
                                <td class="px-4 py-2 text-right">
                                    <form method="POST" action="{{ route('reports.manage.destroy-budget', $b) }}" class="inline" onsubmit="return confirm('Delete this budget entry?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline text-xs">Delete</button></form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    @elseif($tab === 'tax')
        <div class="bg-white rounded-lg border p-5">
            <h3 class="font-semibold mb-3">Filing Status Override</h3>
            <p class="text-xs text-gray-500 mb-3">Tax amounts are auto-computed from your General Ledger (VAT / tax accounts in journal entries), Sales Transactions, and Supplier Bills. Use this form to mark a reference as Filed or Paid.</p>
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
