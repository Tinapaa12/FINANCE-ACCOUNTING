@extends('layouts.app')

@section('title', 'A/R Overview')
@section('page-heading', 'Account Receivable Overview')

@section('content')
<div x-data="overviewApp()" x-cloak>

<div class="flex-1 overflow-y-auto content-scroll p-10 space-y-8 relative bg-dot-grid">
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-200/30 rounded-full blur-3xl pointer-events-none -z-0"></div>
            <div class="absolute top-96 left-0 w-72 h-72 bg-blue-200/20 rounded-full blur-3xl pointer-events-none -z-0"></div>

            <!-- 4 Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 relative">
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-500 to-indigo-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Total Outstanding</p><p class="text-3xl font-bold text-gray-900 mt-1">₱{{ number_format($totalOutstanding) }}</p><p class="text-[13px] text-gray-400 mt-2">Across {{ $invoiceCount }} invoices</p></div>
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-indigo-200 group-hover:scale-105 transition-transform"><i class="fas fa-sack-dollar text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-rose-500 to-red-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Overdue Amount</p><p class="text-3xl font-bold text-gray-900 mt-1">₱{{ number_format($overdueAmount) }}</p><p class="text-[13px] text-gray-400 mt-2">{{ $overdueCount }} invoices past due date</p></div>
                    <div class="bg-gradient-to-br from-rose-500 to-red-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-red-200 group-hover:scale-105 transition-transform"><i class="far fa-clock text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-500 to-green-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Collected this Month</p><p class="text-3xl font-bold text-gray-900 mt-1">₱{{ number_format($collectedThisMonth) }}</p><p class="text-[13px] text-gray-400 mt-2">from {{ $paymentCount }} payments received</p></div>
                    <div class="bg-gradient-to-br from-emerald-500 to-green-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-green-200 group-hover:scale-105 transition-transform"><i class="fas fa-calendar-day text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-purple-500 to-fuchsia-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Avg. Days to Collect</p>                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $avgDaysToCollect }} DAYS</p><p class="text-[13px] text-gray-400 mt-2">DSO - target is 30days</p></div>
                    <div class="bg-gradient-to-br from-purple-500 to-fuchsia-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-purple-200 group-hover:scale-105 transition-transform"><i class="fas fa-calculator text-xl text-white"></i></div>
                </div>
            </div>

<!-- Bottom Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative">
                <!-- Activities Table -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
                    <div class="p-6 pb-4 flex items-center justify-between border-b border-gray-50">
                        <h3 class="font-bold text-gray-800 text-[16px] flex items-center gap-2"><i class="fas fa-clock-rotate-left text-indigo-500"></i> Recent A/R Activities</h3>
                        <span class="text-[11px] font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">Last 7 days</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-[14px] text-left">
                            <thead class="bg-gradient-to-r from-slate-50 to-slate-100/70 text-gray-500">
                                <tr>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Type</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Reference</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Customer</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200 text-right">Amount</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Date</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="activityTableBody">
                                @forelse($recentActivities as $activity)
                                <tr class="transition-colors hover:bg-indigo-50/50 cursor-pointer" data-type="{{ $activity['type'] }}" data-id="{{ $activity['id'] }}" onclick="showTransactionDetail(this)">
                                    <td class="px-6 py-3.5 text-gray-600">
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas w-3.5 text-center {{ $activity['type'] === 'Invoice' ? 'fa-file-invoice text-blue-500' : ($activity['type'] === 'Payment' ? 'fa-hand-holding-usd text-emerald-500' : 'fa-triangle-exclamation text-red-500') }}"></i>
                                            <span>{{ $activity['type'] }}</span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 font-medium text-gray-900">{{ $activity['ref'] }}</td>
                                    <td class="px-6 py-3.5 text-gray-600">{{ $activity['customer'] }}</td>
                                    <td class="px-6 py-3.5 font-medium text-gray-900 text-right tabular-nums">₱{{ number_format($activity['amount']) }}</td>
                                    <td class="px-6 py-3.5 text-gray-500">{{ $activity['date'] }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="px-3 py-1 text-[12px] font-medium rounded-full ring-1 ring-inset
                                              {{ $activity['status'] === 'Sent' ? 'bg-[#fef9c3] text-[#a16207] ring-yellow-200' : '' }}
                                              {{ $activity['status'] === 'Cleared' ? 'bg-[#f0fdf4] text-[#15803d] ring-green-200' : '' }}
                                              {{ $activity['status'] === 'Draft' ? 'bg-[#eff6ff] text-[#1d4ed8] ring-blue-200' : '' }}
                                              {{ $activity['status'] === 'Overdue' ? 'bg-[#fff7ed] text-[#c2410c] ring-orange-200' : '' }}
                                              {{ $activity['status'] === 'Paid' ? 'bg-[#f0fdf4] text-[#15803d] ring-green-200' : '' }}">
                                            {{ $activity['status'] }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No recent activities.</td>
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
                <div class="flex space-x-4">
                    <a href="{{ route('ar.aging') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-[14px] py-2.5 px-5 rounded-md font-medium transition flex items-center justify-center gap-2"><i class="fas fa-chart-line text-indigo-500"></i> View Aging Report</a>
                </div>
                <div class="hidden md:flex items-center gap-2 text-[12px] text-gray-400"><i class="fas fa-circle-info"></i> Data refreshed a few moments ago</div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    function overviewApp() {
        return {
            barsLoaded: false,
            init() {
                setTimeout(() => { this.barsLoaded = true; }, 150);
            },
        }
    }
    function showTransactionDetail(row) {
        const type = row.dataset.type;
        const id = row.dataset.id;
        const apiType = type === 'Payment' ? 'transaction' : 'invoice';
        const content = document.getElementById('detailContent');
        content.innerHTML = '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div></div>';
        fetch('/api/ar/detail?type=' + encodeURIComponent(apiType) + '&id=' + encodeURIComponent(id))
            .then(r => r.json())
            .then(data => {
                if (!data.success) throw new Error(data.message);
                const d = data.data;
                let html = '<div class="space-y-4">';
                html += '<div class="flex items-center gap-2 mb-3"><span class="px-3 py-1 text-[12px] font-medium rounded-full ring-1 ring-inset ' +
                    (d.status === 'Sent' ? 'bg-[#fef9c3] text-[#a16207] ring-yellow-200' : '') +
                    (d.status === 'Cleared' ? 'bg-[#f0fdf4] text-[#15803d] ring-green-200' : '') +
                    (d.status === 'Paid' ? 'bg-[#f0fdf4] text-[#15803d] ring-green-200' : '') +
                    (d.status === 'Overdue' ? 'bg-[#fff7ed] text-[#c2410c] ring-orange-200' : '') +
                    '">' + d.status + '</span></div>';
                html += '<div><p class="text-[11px] text-gray-400 uppercase tracking-wider">Reference</p><p class="text-[14px] font-semibold text-gray-900">' + (d.invoice_number || d.order_no || '--') + '</p></div>';
                html += '<div><p class="text-[11px] text-gray-400 uppercase tracking-wider">Customer</p><p class="text-[14px] font-semibold text-gray-900">' + d.customer + '</p></div>';
                if (d.customer_email) html += '<div><p class="text-[11px] text-gray-400 uppercase tracking-wider">Email</p><p class="text-[13px] text-gray-600">' + d.customer_email + '</p></div>';
                if (d.customer_phone) html += '<div><p class="text-[11px] text-gray-400 uppercase tracking-wider">Phone</p><p class="text-[13px] text-gray-600">' + d.customer_phone + '</p></div>';
                html += '<div class="grid grid-cols-2 gap-3">';
                html += '<div><p class="text-[11px] text-gray-400 uppercase tracking-wider">Amount</p><p class="text-[15px] font-bold text-gray-900 tabular-nums">₱' + Number(d.total || d.total_amount || 0).toLocaleString() + '</p></div>';
                if (d.payment_method) html += '<div><p class="text-[11px] text-gray-400 uppercase tracking-wider">Method</p><p class="text-[13px] text-gray-600">' + d.payment_method + '</p></div>';
                html += '</div>';
                html += '<div><p class="text-[11px] text-gray-400 uppercase tracking-wider">Date</p><p class="text-[13px] text-gray-600">' + (d.invoice_date || d.created_at || '--') + '</p></div>';
                if (d.due_date) html += '<div><p class="text-[11px] text-gray-400 uppercase tracking-wider">Due Date</p><p class="text-[13px] text-gray-600">' + d.due_date + '</p></div>';
                if (d.days_overdue !== undefined && d.days_overdue > 0) html += '<div class="text-[12px] text-red-500 font-medium">Overdue by ' + d.days_overdue + ' day(s)</div>';
                html += '</div>';
                content.innerHTML = html;
            })
            .catch(() => {
                content.innerHTML = '<div class="text-center py-8 text-gray-400"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p class="text-[13px]">Failed to load details</p></div>';
            });
    }
</script>
@endpush
