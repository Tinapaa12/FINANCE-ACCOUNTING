@extends('layouts.app')

@section('title', 'Payments Received')
@section('page-heading', 'Accounts Receivable')

@section('content')
<div x-data="paymentApp()" x-cloak>

<div class="flex-1 overflow-y-auto content-scroll p-10 space-y-8 relative bg-dot-grid">
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-200/30 rounded-full blur-3xl pointer-events-none -z-0"></div>
            <div class="absolute top-96 left-0 w-72 h-72 bg-emerald-200/20 rounded-full blur-3xl pointer-events-none -z-0"></div>

            <div class="flex justify-between items-center relative">
                <a href="{{ route('ar.overview') }}" class="text-[14px] text-gray-500 hover:text-[#2563eb] font-medium flex items-center gap-2"><i class="fas fa-arrow-left"></i> Back to A/R Overview</a>
                
            </div>

            <!-- 4 Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 relative">
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-500 to-green-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Total Collected (This Month)</p><p class="text-3xl font-bold text-gray-900 mt-1">₱{{ number_format($monthlyTotal) }}</p><p class="text-[13px] text-gray-400 mt-2">From {{ $monthlyCount }} {{ Str::plural('payment', $monthlyCount) }}</p></div>
                    <div class="bg-gradient-to-br from-emerald-500 to-green-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-green-200 group-hover:scale-105 transition-transform"><i class="fas fa-hand-holding-usd text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-500 to-indigo-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Cleared Payments</p><p class="text-3xl font-bold text-gray-900 mt-1">{{ $clearedCount }}</p><p class="text-[13px] text-gray-400 mt-2">Fully reconciled</p></div>
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-indigo-200 group-hover:scale-105 transition-transform"><i class="fas fa-check-circle text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-amber-400 to-orange-500"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Pending Clearance</p><p class="text-3xl font-bold text-gray-900 mt-1">{{ $transactions->where('status', 'Pending')->count() }}</p><p class="text-[13px] text-gray-400 mt-2">₱{{ number_format($pendingAmount) }}{{ $pendingCustomer ? ' - ' . $pendingCustomer : '' }}</p></div>
                    <div class="bg-gradient-to-br from-amber-400 to-orange-500 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-orange-200 group-hover:scale-105 transition-transform"><i class="fas fa-hourglass-half text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-purple-500 to-fuchsia-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Top Payment Method</p><p class="text-3xl font-bold text-[#4338ca] mt-1">{{ $topMethod ? $topMethod['label'] : 'N/A' }}</p><p class="text-[13px] text-gray-400 mt-2">{{ $topMethod ? '₱' . number_format($topMethod['amount']) : '' }}</p></div>
                    <div class="bg-gradient-to-br from-purple-500 to-fuchsia-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-purple-200 group-hover:scale-105 transition-transform"><i class="fas fa-mobile-screen text-xl text-white"></i></div>
                </div>
            </div>

<!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative">
                <!-- Transactions Table -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
                    <div class="p-6 pb-4 flex items-center justify-between border-b border-gray-50">
                        <h3 class="font-bold text-gray-800 text-[16px] flex items-center gap-2"><i class="fas fa-receipt text-indigo-500"></i> Payments Received</h3>
                        <span class="text-[11px] font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">{{ $transactions->count() }} records</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-[14px] text-left">
                            <thead class="bg-gradient-to-r from-slate-50 to-slate-100/70 text-gray-500">
                                <tr>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Order No</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Customer</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200 text-right">Amount</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Method</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($transactions as $txn)
                                <tr class="hover:bg-indigo-50/50 transition-colors cursor-pointer" data-type="transaction" data-id="{{ $txn->sales_transaction_id }}" onclick="showTransactionDetail(this)">
                                    <td class="px-6 py-3.5 font-medium text-gray-800">{{ $txn->order_no }}</td>
                                    <td class="px-6 py-3.5 text-gray-600">{{ $txn->customer_name }}</td>
                                    <td class="px-6 py-3.5 font-medium text-gray-900 text-right tabular-nums">₱{{ number_format($txn->total_amount, 2) }}</td>
                                    <td class="px-6 py-3.5 text-gray-700">{{ $txn->payment_method }}</td>
                                    <td class="px-6 py-3.5">
                                        @php
                                        $statusColors = [
                                            'Pending' => 'bg-[#fef9c3] text-[#a16207] ring-yellow-200',
                                            'Paid' => 'bg-[#f0fdf4] text-[#15803d] ring-green-200',
                                            'Pay Later' => 'bg-[#fef9c3] text-[#a16207] ring-yellow-200',
                                        ];
                                        $statusLabel = $txn->payment_method === 'Pay Later' && $txn->status === 'Pending' ? 'Pay Later' : $txn->status;
                                        @endphp
                                        <span class="px-3 py-1 text-[12px] font-medium rounded-full ring-1 ring-inset {{ $statusColors[$statusLabel] ?? 'bg-gray-100 text-gray-600 ring-gray-200' }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No sales transactions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Transaction Detail Panel -->
                <div class="bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 h-fit relative overflow-hidden" id="detailPanel">
                    <div class="absolute -top-8 -right-8 w-28 h-28 bg-indigo-50 rounded-full"></div>
                    <h3 class="font-bold text-gray-800 text-[16px] mb-5 flex items-center gap-2 relative"><i class="fas fa-circle-info text-indigo-500"></i> Transaction Details</h3>
                    <div id="detailContent" class="relative">
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                            <i class="fas fa-arrow-left text-2xl mb-3"></i>
                            <p class="text-[13px]">Click a transaction to view details</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="bg-gradient-to-r from-white to-slate-50 p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex items-center justify-between">
                <div class="hidden md:flex items-center gap-2 text-[12px] text-gray-400"><i class="fas fa-circle-info"></i> Data refreshed a few moments ago</div>
            </div>
        </div>

</div>

    <div x-show="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 p-6 relative">
            <button @click="showPaymentModal = false; window.location.reload()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            <h3 class="text-lg font-bold text-gray-900 mb-4">Payment Notice</h3>
            <template x-if="paymentData">
                <div class="space-y-3">
                    <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <p class="text-sm text-gray-800 leading-relaxed" x-text="paymentData.message"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div><span class="text-gray-500">Customer:</span> <span class="font-medium" x-text="paymentData.customer"></span></div>
                        <div><span class="text-gray-500">Order:</span> <span class="font-medium" x-text="paymentData.order_no"></span></div>
                        <div><span class="text-gray-500">Phone:</span> <span class="font-medium" x-text="paymentData.phone || 'N/A'"></span></div>
                        <div><span class="text-gray-500">Total Amount:</span> <span class="font-medium" x-text="'₱' + Number(paymentData.total_amount).toLocaleString()"></span></div>
                        <div><span class="text-gray-500">Initial Payment:</span> <span class="font-medium" x-text="'₱' + Number(paymentData.initial_payment).toLocaleString()"></span></div>
                        <div><span class="text-gray-500">Remaining Paid:</span> <span class="font-medium" x-text="'₱' + Number(paymentData.remaining_paid).toLocaleString()"></span></div>
                    </div>
                </div>
            </template>
            <div class="mt-4 flex justify-end">
                <button @click="showPaymentModal = false; window.location.reload()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium text-sm">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function paymentApp() {
        return {
            showPaymentModal: false,
            paymentData: null,
            async markAsPaid(id) {
                if (!confirm('Mark this transaction as Paid?')) return;
                try {
                    const res = await fetch('/sales-transactions/' + id + '/mark-as-paid', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();
                    if (res.ok) {
                        if (data.payment_data) {
                            this.paymentData = data.payment_data;
                            this.showPaymentModal = true;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        alert('Error: ' + (data.message || 'Request failed'));
                    }
                } catch (e) {
                    alert('Network error - check console for details');
                    console.error(e);
                }
            },
        }
    }
    function showTransactionDetail(row) {
        const id = row.dataset.id;
        const content = document.getElementById('detailContent');
        content.innerHTML = '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div></div>';
        fetch('/api/ar/detail?type=transaction&id=' + encodeURIComponent(id))
            .then(r => r.json())
            .then(data => {
                if (!data.success) throw new Error(data.message);
                const d = data.data;
                let html = '<div class="space-y-4">';
                html += '<div class="flex items-center gap-2 mb-3"><span class="px-3 py-1 text-[12px] font-medium rounded-full ring-1 ring-inset ' +
                    (d.status === 'Paid' ? 'bg-[#f0fdf4] text-[#15803d] ring-green-200' : '') +
                    (d.status === 'Pending' ? 'bg-[#fef9c3] text-[#a16207] ring-yellow-200' : '') +
                    '">' + d.status + '</span></div>';
                html += '<div><p class="text-[11px] text-gray-400 uppercase tracking-wider">Order No</p><p class="text-[14px] font-semibold text-gray-900">' + (d.order_no || '--') + '</p></div>';
                html += '<div><p class="text-[11px] text-gray-400 uppercase tracking-wider">Customer</p><p class="text-[14px] font-semibold text-gray-900">' + d.customer + '</p></div>';
                if (d.phone) html += '<div><p class="text-[11px] text-gray-400 uppercase tracking-wider">Phone</p><p class="text-[13px] text-gray-600">' + d.phone + '</p></div>';
                html += '<div class="grid grid-cols-2 gap-3">';
                html += '<div><p class="text-[11px] text-gray-400 uppercase tracking-wider">Total Amount</p><p class="text-[15px] font-bold text-gray-900 tabular-nums">₱' + Number(d.total_amount).toLocaleString() + '</p></div>';
                html += '<div><p class="text-[11px] text-gray-400 uppercase tracking-wider">Method</p><p class="text-[13px] text-gray-600">' + (d.payment_method || '--') + '</p></div>';
                html += '</div>';
                html += '<div><p class="text-[11px] text-gray-400 uppercase tracking-wider">Date</p><p class="text-[13px] text-gray-600">' + (d.created_at || '--') + '</p></div>';
                if (d.status === 'Pending') {
                    html += '<div class="pt-3 border-t border-gray-100"><button onclick="markAsPaid(' + d.id + ')" class="w-full bg-blue-500 hover:bg-blue-600 text-white text-[13px] py-2.5 px-4 rounded-lg font-medium transition flex items-center justify-center gap-2"><i class="fas fa-check"></i> Mark as Paid</button></div>';
                }
                html += '</div>';
                content.innerHTML = html;
            })
            .catch(() => {
                content.innerHTML = '<div class="text-center py-8 text-gray-400"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p class="text-[13px]">Failed to load details</p></div>';
            });
    }
    async function markAsPaid(id) {
        if (!confirm('Mark this transaction as Paid?')) return;
        try {
            const res = await fetch('/sales-transactions/' + id + '/mark-as-paid', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
            const data = await res.json();
            if (res.ok) {
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Request failed'));
            }
        } catch (e) {
            alert('Network error - check console for details');
            console.error(e);
        }
    }
</script>
@endpush
