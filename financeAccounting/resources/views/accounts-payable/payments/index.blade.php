@extends('layouts.app')

@section('page-title', 'Payments Made')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold">Payments Made to Suppliers</h2>
        <form method="GET" action="{{ route('payments.index') }}">
            <input type="text" name="search" placeholder="Search by supplier, bill, method, ref..." value="{{ request('search') }}" class="w-80 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </form>
    </div>

    <table class="w-full border-collapse">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">#</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Bill No.</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Supplier</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Amount</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Payment Date</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Method</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Reference</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Receipt</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            @php $bill = $payment->supplierBill;         @endphp
            <tr class="hover:bg-gray-50 transition-colors"
                data-id="{{ $payment->id }}"
                data-bill-no="{{ $bill?->bill_no ?? 'N/A' }}"
                data-supplier="{{ $bill?->supplier ?? 'N/A' }}"
                data-amount="{{ $payment->amount }}"
                data-payment-date="{{ $payment->payment_date?->format('M d, Y') ?? 'N/A' }}"
                data-method="{{ $payment->payment_method ?: 'Cash' }}"
                data-reference="{{ $payment->reference ?: '-' }}">
                <td class="px-4 py-4 border-t border-gray-100 text-sm text-gray-500">{{ $payment->id }}</td>
                <td class="px-4 py-4 border-t border-gray-100 text-sm font-medium">{{ $bill?->bill_no ?? 'N/A' }}</td>
                <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $bill?->supplier ?? 'N/A' }}</td>
                <td class="px-4 py-4 border-t border-gray-100 text-sm">₱{{ number_format($payment->amount, 2) }}</td>
                <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : 'N/A' }}</td>
                <td class="px-4 py-4 border-t border-gray-100 text-sm"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $payment->payment_method ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-500' }}">{{ $payment->payment_method ?: 'N/A' }}</span></td>
                <td class="px-4 py-4 border-t border-gray-100 text-sm text-gray-500">{{ $payment->reference ?: '-' }}</td>
                <td class="px-4 py-4 border-t border-gray-100 text-sm">
                    <button type="button" onclick="showPaymentReceipt({{ $payment->id }}, this)"
                        class="border-none cursor-pointer rounded-md bg-blue-600 text-white px-3 py-1.5 text-xs font-medium hover:bg-blue-700 transition-colors">View Receipt</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center px-4 py-8 text-gray-500">No payments made yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $payments->appends(request()->query())->links() }}
    </div>

</div>

<!-- Voucher Modal -->
<div id="receiptModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
    <div class="w-[500px] bg-white rounded-xl shadow-xl p-8" id="receiptContent">
        <div class="text-center border-b border-gray-200 pb-4 mb-4">
            <h2 class="text-2xl font-bold">PAYMENT RECEIPT</h2>
            <p class="text-sm text-gray-500">Official Payment Receipt</p>
        </div>
        <div class="space-y-3">
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Receipt No.</span><span id="receiptNo" class="text-gray-900 font-semibold"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Bill No.</span><span id="receiptBillNo" class="text-gray-900"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Supplier</span><span id="receiptSupplier" class="text-gray-900"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Amount Paid</span><span id="receiptAmount" class="text-gray-900 font-bold text-lg"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Payment Date</span><span id="receiptDate" class="text-gray-900"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Payment Method</span><span id="receiptMethod" class="text-gray-900"></span></div>
            <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Reference</span><span id="receiptReference" class="text-gray-900"></span></div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button type="button" onclick="printReceipt()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Print</button>
            <button type="button" onclick="closeReceiptModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Close</button>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #receiptContent, #receiptContent * { visibility: visible; }
    #receiptContent { position: fixed; left: 0; top: 0; width: 100%; padding: 40px; box-shadow: none; }
    #receiptContent .flex.justify-end.gap-3 { display: none !important; }
}
</style>

<script>
function showPaymentReceipt(id, btn) {
    var row = btn.closest('tr');
    document.getElementById('receiptNo').textContent = 'RCT-' + String(id).padStart(4, '0');
    document.getElementById('receiptBillNo').textContent = row.getAttribute('data-bill-no');
    document.getElementById('receiptSupplier').textContent = row.getAttribute('data-supplier');
    document.getElementById('receiptAmount').textContent = '₱' + Number(row.getAttribute('data-amount')).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('receiptDate').textContent = row.getAttribute('data-payment-date');
    document.getElementById('receiptMethod').textContent = row.getAttribute('data-method');
    document.getElementById('receiptReference').textContent = row.getAttribute('data-reference');
    document.getElementById('receiptModal').classList.add('active');
}
function closeReceiptModal() { document.getElementById('receiptModal').classList.remove('active'); }
document.getElementById('receiptModal')?.addEventListener('click', function(e) { if (e.target === this) closeReceiptModal(); });
function printReceipt() { window.print(); }
</script>

@endsection