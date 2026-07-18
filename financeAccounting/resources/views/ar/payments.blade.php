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
                <button @click="showPaymentModal = true" class="bg-gradient-to-r from-[#2563eb] to-[#4338ca] hover:brightness-110 text-white text-[14px] font-medium py-2.5 px-5 rounded-md transition shadow-md shadow-indigo-200 flex items-center gap-2"><i class="fas fa-plus"></i> Record Payment</button>
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
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 relative">
                <!-- Transactions Table -->
                <div class="lg:col-span-3 bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
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
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($transactions as $txn)
                                <tr class="hover:bg-indigo-50/50 transition-colors">
                                    <td class="px-6 py-3.5 font-medium text-gray-800">{{ $txn->order_no }}</td>
                                    <td class="px-6 py-3.5 text-gray-600">{{ $txn->customer_name }}</td>
                                    <td class="px-6 py-3.5 font-medium text-gray-900 text-right tabular-nums">₱{{ number_format($txn->total_amount, 2) }}</td>
                                    <td class="px-6 py-3.5 text-gray-700">{{ $txn->payment_method }}</td>
                                    <td class="px-6 py-3.5">
                                        @php
                                        $statusColors = [
                                            'Draft' => 'bg-[#eff6ff] text-[#1d4ed8] ring-blue-200',
                                            'Sent' => 'bg-[#fef9c3] text-[#a16207] ring-yellow-200',
                                            'Overdue' => 'bg-[#fff7ed] text-[#c2410c] ring-orange-200',
                                            'Cleared' => 'bg-[#f0fdf4] text-[#15803d] ring-green-200',
                                            'Paid' => 'bg-[#f0fdf4] text-[#15803d] ring-green-200',
                                        ];
                                        @endphp
                                        <span class="px-3 py-1 text-[12px] font-medium rounded-full ring-1 ring-inset {{ $statusColors[$txn->status] ?? 'bg-gray-100 text-gray-600 ring-gray-200' }}">
                                            {{ $txn->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        @if(!in_array($txn->status, ['Paid', 'Cleared']))
                                            <button @click="markAsPaid({{ $txn->sales_transaction_id }})" class="text-xs font-medium text-blue-600 hover:text-blue-800">Mark as Paid</button>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-green-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Completed
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No sales transactions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Method Breakdown -->
                <div class="bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 h-fit relative overflow-hidden">
                    <div class="absolute -top-8 -right-8 w-28 h-28 bg-indigo-50 rounded-full"></div>
                    <h3 class="font-bold text-gray-800 text-[15px] mb-4 flex items-center gap-2 relative"><i class="fas fa-chart-pie text-indigo-500"></i> Payment Method Breakdown</h3>
                    <div class="space-y-4 mb-4 relative">
                        @forelse($methodBreakdown as $method)
                        <div class="flex items-center text-[13px]">
                            <span class="w-14 text-gray-600 font-medium">{{ $method['label'] }}</span>
                            <div class="flex-1 h-3 bg-gray-100 rounded-full mx-3 overflow-hidden">
                                <div class="h-full rounded-full transition-all ease-out duration-1000"
                                     x-init="$el.style.width = '{{ $method['pct'] }}%'; $el.style.backgroundColor = '{{ $method['color'] }}'"
                                     style="width: 0%; background-color: {{ $method['color'] }}"></div>
                            </div>
                            <span class="font-medium text-gray-800 tabular-nums">₱{{ number_format($method['amount']) }}</span>
                        </div>
                        @empty
                        <p class="text-sm text-gray-400 text-center py-4">No payment data</p>
                        @endforelse
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-between text-[14px] font-bold relative">
                        <span class="text-gray-800">Total Received</span><span class="text-[#4338ca]">₱{{ number_format($grandTotal) }}</span>
                    </div>
                </div>
            </div>
        </div>

<!-- RECORD PAYMENT MODAL -->
    <div x-show="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" x-cloak @click.away="showPaymentModal = false">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl p-6" @click.stop>
            <div class="flex items-center gap-3 border-b pb-4 mb-4">
                <i class="fas fa-hand-holding-usd text-xl text-white bg-gradient-to-br from-[#2563eb] to-[#4338ca] p-2 rounded-lg"></i>
                <h2 class="text-lg font-bold text-gray-800">Record Payment</h2>
                <button @click="showPaymentModal = false" class="ml-auto text-gray-400 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="space-y-4 text-[14px]">
                <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Customer <span class="text-red-500">*</span></label><select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]"><option>-- Select Customer --</option><option>ABC Trading Co.</option><option>Cruz & Sons</option><option>Lim Trading</option><option>Reyes Corp</option><option>Santos Enterprise</option></select></div>
                <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Apply to Invoice <span class="text-red-500">*</span></label><select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]"><option>-- Select Invoice --</option><option>INV-0001 - P45,000 (Overdue)</option><option>INV-0022 - P38,500 (Sent)</option><option>INV-0024 - P78,000 (Draft)</option><option>INV-0025 - P19,500 (Sent)</option></select></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Amount Received <span class="text-red-500">*</span></label><input type="number" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]" placeholder="0.00"></div>
                    <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Payment Date <span class="text-red-500">*</span></label><input type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]" value="2026-07-07"></div>
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Payment Method <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-3">
                        <label class="flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-50 bg-[#f9fafb]"><input type="radio" name="method" value="Bank" checked> <i class="fas fa-university text-[14px]"></i> Bank</label>
                        <label class="flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-50 bg-[#f9fafb]"><input type="radio" name="method" value="GCash"> <i class="fas fa-mobile-screen text-[14px]"></i> GCash</label>
                        <label class="flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-50 bg-[#f9fafb]"><input type="radio" name="method" value="Check"> <i class="fas fa-money-check text-[14px]"></i> Check</label>
                        <label class="flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-50 bg-[#f9fafb]"><input type="radio" name="method" value="Cash"> <i class="fas fa-money-bill-wave text-[14px]"></i> Cash</label>
                    </div>
                </div>
                <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Notes <span class="text-gray-400 font-normal">(Optional)</span></label><textarea class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none text-[14px] bg-[#f9fafb]" rows="2" placeholder="e.g. Partial Payment for June Delivery"></textarea></div>
            </div>
            <div class="mt-6 flex justify-end space-x-3 border-t pt-4">
                <button @click="showPaymentModal = false" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-600 text-[14px] hover:bg-gray-50 transition">Cancel</button>
                <button @click="showPaymentModal = false; alert('Payment Recorded Successfully!')" class="px-6 py-2.5 bg-gradient-to-r from-[#2563eb] to-[#4338ca] hover:brightness-110 text-white rounded-lg text-[14px] font-medium transition shadow-sm">Record</button>
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

                async markAsPaid(id) {
                    if (!confirm('Mark this transaction as Paid?')) return;
                    try {
                        const res = await fetch('/sales-transactions/' + id + '/mark-as-paid', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        });
                        if (res.ok) {
                            window.location.reload();
                        } else {
                            const data = await res.json();
                            alert(data.message || 'Failed to mark as paid');
                        }
                    } catch (e) {
                        alert('Network error');
                    }
                },
            }
        }
</script>
@endpush
