@extends('layouts.app')

@section('title', 'API Playground')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
        Test endpoints: <code>POST /api/management/budget</code> (single budget entry) and
        <code>POST /api/seed-demo</code> (populates ALL modules with demo data).
    </div>

    <div class="bg-white rounded-lg border p-5">
        <h3 class="font-semibold mb-3">Seed Everything — All Modules</h3>
        <p class="text-xs text-gray-500 mb-3">Clears existing data and creates fresh demo records for COA, JEs, POs, GRNs, bills, payments, sales, budget, and tax.</p>
        <form method="POST" action="{{ route('api-playground.seed') }}">
            @csrf
            <button type="submit" class="bg-purple-600 text-white px-4 py-1.5 rounded text-sm hover:bg-purple-700" onclick="return confirm('This will DELETE all existing data and re-seed. Continue?')">Seed Everything (via API)</button>
        </form>
        <button type="button" id="seedFetchBtn" class="mt-2 bg-gray-600 text-white px-4 py-1.5 rounded text-sm hover:bg-gray-700">Test seed via fetch()</button>
        <div id="seedResponse" class="mt-4 hidden"></div>
    </div>

    <div class="bg-white rounded-lg border p-5">
        <h3 class="font-semibold mb-3">migrate:fresh --seed</h3>
        <p class="text-xs text-gray-500 mb-3">Drops all tables, re-runs migrations, then seeds. Use this when you change table schemas.</p>
        <form method="POST" action="{{ route('api-playground.migrate-fresh') }}">
            @csrf
            <button type="submit" class="bg-red-600 text-white px-4 py-1.5 rounded text-sm hover:bg-red-700" onclick="return confirm('This will DELETE ALL TABLES and data. Continue?')">Run migrate:fresh --seed</button>
        </form>
        <button type="button" id="migrateFetchBtn" class="mt-2 bg-gray-600 text-white px-4 py-1.5 rounded text-sm hover:bg-gray-700">Test via fetch()</button>
        <div id="migrateResponse" class="mt-4 hidden"></div>
    </div>

    <div class="bg-white rounded-lg border p-5">
        <h3 class="font-semibold mb-3">Submit Single Budget via API</h3>

        <form method="POST" action="{{ route('api-playground.submit') }}" class="flex gap-3 items-end flex-wrap">
            @csrf
            <div>
                <label class="text-xs text-gray-500 block mb-1">Account</label>
                <select name="account_code" required class="border rounded px-3 py-1.5 text-sm min-w-[200px]">
                    <option value="">Select account</option>
                    @foreach($accounts as $a)
                        <option value="{{ $a->account_code }}">{{ $a->account_code }} - {{ $a->account_name }} ({{ $a->type }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 block mb-1">Budget Amount (₱)</label>
                <input type="number" name="budget_amount" step="0.01" min="0" required class="border rounded px-3 py-1.5 text-sm">
            </div>
            <div>
                <label class="text-xs text-gray-500 block mb-1">Period</label>
                <select name="period" required class="border rounded px-3 py-1.5 text-sm">
                    <option value="">Select period</option>
                    @forelse($periods as $p)
                        <option value="{{ $p }}">{{ $p }}</option>
                    @empty
                        <option value="{{ now()->format('F Y') }}">{{ now()->format('F Y') }}</option>
                    @endforelse
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Submit via API</button>
            <button type="button" id="fetchBtn" class="bg-gray-600 text-white px-4 py-1.5 rounded text-sm hover:bg-gray-700">Test via fetch()</button>
        </form>

        <div id="response" class="mt-4 hidden"></div>
    </div>

    <div class="bg-white rounded-lg border p-5">
        <h3 class="font-semibold mb-3">API Log</h3>
        <div id="log" class="text-xs font-mono bg-gray-900 text-green-400 rounded-lg p-4 max-h-64 overflow-y-auto">Awaiting request...</div>
    </div>

    <div class="bg-white rounded-lg border p-5">
        <h3 class="font-semibold mb-3">Current Budget Entries</h3>
        @php $entries = \App\Models\FinancialReporting\BudgetVsActual::orderByDesc('report_period_start')->get(); @endphp
        @if($entries->isEmpty())
            <p class="text-sm text-gray-500">No entries yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b text-left text-gray-500"><th class="py-2 px-3">Account</th><th class="py-2 px-3 text-right">Budget</th><th class="py-2 px-3 text-right">Actual</th><th class="py-2 px-3 text-right">Period</th></tr></thead>
                    <tbody>
                        @foreach($entries as $e)
                            <tr class="border-t"><td class="px-3 py-2">{{ $e->account_name }}</td><td class="px-3 py-2 text-right">{{ number_format($e->budget_amount, 2) }}</td><td class="px-3 py-2 text-right">{{ number_format($e->actual_amount, 2) }}</td><td class="px-3 py-2 text-right">{{ \Carbon\Carbon::parse($e->report_period_start)->format('F Y') }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
async function apiCall(method, url, payload, logId, responseId) {
    const log = document.getElementById(logId);
    const resp = document.getElementById(responseId);
    log.innerHTML = 'Sending: <span class="text-yellow-300">' + method + ' ' + url + '</span>\n' + (payload ? 'Payload: ' + JSON.stringify(payload, null, 2) + '\n\n' : '\n');
    resp.classList.add('hidden');
    try {
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-API-Key': 'mgmt-secret-key' },
            body: payload ? JSON.stringify(payload) : undefined,
        });
        const data = await res.json();
        log.innerHTML += 'Status: <span class="' + (res.ok ? 'text-green-300' : 'text-red-300') + '">' + res.status + '</span>\nResponse: ' + JSON.stringify(data, null, 2);
        resp.className = 'mt-4 ' + (res.ok ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800') + ' border rounded-lg p-4 text-sm';
        if (res.ok && url.includes('seed-demo')) {
            resp.innerHTML = '✓ Demo data seeded! <a href="" class="underline">Refresh page</a><br><span class="text-xs">' + JSON.stringify(data.stats) + '</span>';
        } else if (res.ok) {
            resp.innerHTML = '✓ Done! <a href="" class="underline">Refresh page</a>';
        } else {
            resp.innerHTML = '<strong>Error:</strong> ' + (data.message || JSON.stringify(data.errors || data));
        }
        resp.classList.remove('hidden');
    } catch (err) {
        log.innerHTML += '\n<span class="text-red-300">Network error: ' + err.message + '</span>';
    }
}

document.getElementById('fetchBtn')?.addEventListener('click', function() {
    const form = this.closest('form');
    apiCall('POST', '/api/management/budget', {
        account_code: form.account_code.value,
        budget_amount: parseFloat(form.budget_amount.value),
        period: form.period.value,
    }, 'log', 'response');
});

document.getElementById('seedFetchBtn')?.addEventListener('click', function() {
    if (!confirm('This will DELETE all existing data and re-seed. Continue?')) return;
    apiCall('POST', '/api/seed-demo', null, 'log', 'seedResponse');
});

document.getElementById('migrateFetchBtn')?.addEventListener('click', function() {
    if (!confirm('This will DELETE ALL TABLES and data. Continue?')) return;
    apiCall('POST', '/api/migrate-fresh', null, 'log', 'migrateResponse');
});
</script>
@endsection