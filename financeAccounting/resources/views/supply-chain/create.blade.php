@extends('layouts.app')

@section('title', 'Supply Chain Simulator')
@section('page-title', 'Supply Chain Simulator')

@section('content')
<div class="max-w-2xl mx-auto">
    <div id="successMessage" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="mx-auto mb-5 w-20 h-20 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Bill Submitted Successfully</h3>
        <p id="successText" class="text-gray-600 mb-6"></p>
        <div class="flex items-center justify-center gap-3">
            <button onclick="resetForm()" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Another Bill
            </button>
            <a href="{{ route('supplier-bills.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-500 text-white rounded-lg hover:bg-green-600 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                View Supplier Bills
            </a>
        </div>
    </div>

    <div id="formCard" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">Submit Supplier Bill</h3>
            <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium">Simulating Supply Chain Module</span>
        </div>

        <form id="billForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                <input id="supplier" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="e.g. Acme Corp" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₱) <span class="text-red-500">*</span></label>
                <input id="amount" type="number" step="0.01" min="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="0.00" required>
                <p id="amountError" class="text-xs text-red-500 mt-1 hidden"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date <span class="text-red-500">*</span></label>
                <input id="due_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Expense Account <span class="text-red-500">*</span></label>
                <select id="expense_account_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                    <option value="">Select expense account...</option>
                    @foreach($expenseAccounts as $account)
                        <option value="{{ $account->account_id }}">{{ $account->account_code }} — {{ $account->account_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" id="submitBtn" class="px-6 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium flex items-center gap-2">
                    <span id="btnText">Submit to Finance</span>
                    <svg id="btnSpinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('due_date').value = new Date().toISOString().split('T')[0];
});

document.getElementById('billForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const amount = document.getElementById('amount').value;
    const amountError = document.getElementById('amountError');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    if (!amount || parseFloat(amount) < 0.01) {
        amountError.textContent = 'Please enter a valid amount (minimum 0.01).';
        amountError.classList.remove('hidden');
        return;
    }
    amountError.classList.add('hidden');

    submitBtn.disabled = true;
    btnText.textContent = 'Submitting...';
    btnSpinner.classList.remove('hidden');

    try {
        const response = await fetch('/api/supplier-bills', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                supplier: document.getElementById('supplier').value,
                amount: amount,
                due_date: document.getElementById('due_date').value,
                status: document.getElementById('status').value,
                expense_account_id: document.getElementById('expense_account_id').value,
            }),
        });

        if (!response.ok) {
            const text = await response.text();
            let msg;
            try { msg = JSON.parse(text).message; } catch (e) { msg = text || 'Request failed'; }
            alert('Error: ' + msg);
            return;
        }

        const result = await response.json();

        if (result.success) {
            document.getElementById('formCard').classList.add('hidden');
            document.getElementById('successText').textContent = result.message;
            document.getElementById('successMessage').classList.remove('hidden');
        } else {
            alert('Error: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        alert('Network error: ' + error.message);
    } finally {
        submitBtn.disabled = false;
        btnText.textContent = 'Submit to Finance';
        btnSpinner.classList.add('hidden');
    }
});

function resetForm() {
    document.getElementById('billForm').reset();
    document.getElementById('due_date').value = new Date().toISOString().split('T')[0];
    document.getElementById('successMessage').classList.add('hidden');
    document.getElementById('formCard').classList.remove('hidden');
    document.getElementById('amountError').classList.add('hidden');
}
</script>
@endpush
