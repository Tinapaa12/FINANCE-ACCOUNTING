@extends('layouts.app')

@section('title', 'API Playground')

@section('content')
<div class="space-y-6">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
        This page sends data directly to <code>/api/management/budget</code> via <code>fetch()</code>
        with the <code>X-API-Key</code> header. Results go straight to the DB.
    </div>

    <div class="bg-white rounded-lg border p-5">
        <h3 class="font-semibold mb-3">Submit Budget via API</h3>

        <form id="apiForm" class="flex gap-3 items-end flex-wrap">
            <div>
                <label class="text-xs text-gray-500 block mb-1">Account</label>
                <select id="account_code" required class="border rounded px-3 py-1.5 text-sm min-w-[200px]">
                    <option value="">Select account</option>
                    @foreach($accounts as $a)
                        <option value="{{ $a->account_code }}">{{ $a->account_code }} - {{ $a->account_name }} ({{ $a->type }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 block mb-1">Budget Amount (₱)</label>
                <input type="number" id="budget_amount" step="0.01" min="0" required class="border rounded px-3 py-1.5 text-sm">
            </div>
            <div>
                <label class="text-xs text-gray-500 block mb-1">Period</label>
                <select id="period" required class="border rounded px-3 py-1.5 text-sm">
                    <option value="">Select period</option>
                    @forelse($periods as $p)
                        <option value="{{ $p }}">{{ $p }}</option>
                    @empty
                        <option value="{{ now()->format('F Y') }}">{{ now()->format('F Y') }}</option>
                    @endforelse
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Send via API</button>
        </form>

        <div id="response" class="mt-4 hidden"></div>
    </div>

    <div class="bg-white rounded-lg border p-5">
        <h3 class="font-semibold mb-3">API Log</h3>
        <div id="log" class="text-xs font-mono bg-gray-900 text-green-400 rounded-lg p-4 max-h-64 overflow-y-auto">Awaiting request...</div>
    </div>
</div>

<script>
document.getElementById('apiForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const payload = {
        account_code: document.getElementById('account_code').value,
        budget_amount: parseFloat(document.getElementById('budget_amount').value),
        period: document.getElementById('period').value,
    };

    const log = document.getElementById('log');
    const responseDiv = document.getElementById('response');

    log.innerHTML = 'Sending: <span class="text-yellow-300">POST /api/management/budget</span>\n' +
        'Payload: ' + JSON.stringify(payload, null, 2) + '\n\n';

    responseDiv.classList.add('hidden');

    try {
        const res = await fetch('/api/management/budget', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-API-Key': 'mgmt-secret-key',
            },
            body: JSON.stringify(payload),
        });

        const data = await res.json();

        log.innerHTML += 'Status: <span class="' + (res.ok ? 'text-green-300' : 'text-red-300') + '">' + res.status + ' ' + res.statusText + '</span>\n';
        log.innerHTML += 'Response: ' + JSON.stringify(data, null, 2);

        if (res.ok) {
            responseDiv.className = 'mt-4 bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800';
            responseDiv.textContent = '✓ Budget entry created via API! Refreshing list...';
            responseDiv.classList.remove('hidden');
            setTimeout(() => responseDiv.classList.add('hidden'), 3000);
        } else {
            responseDiv.className = 'mt-4 bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-800';
            responseDiv.innerHTML = '<strong>Error:</strong> ' + (data.message || JSON.stringify(data.errors || data));
            responseDiv.classList.remove('hidden');
        }
    } catch (err) {
        log.innerHTML += '\n<span class="text-red-300">Network error: ' + err.message + '</span>';
    }
});
</script>
@endsection