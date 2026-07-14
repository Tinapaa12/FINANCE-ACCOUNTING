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
                    <div><p class="text-[13px] text-gray-500 font-medium">Total Collected (This Month)</p><p class="text-3xl font-bold text-gray-900 mt-1">P{{ number_format($collectedThisMonth) }}</p><p class="text-[13px] text-gray-400 mt-2">From {{ $collectedCount }} payments</p></div>
                    <div class="bg-gradient-to-br from-emerald-500 to-green-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-green-200 group-hover:scale-105 transition-transform"><i class="fas fa-hand-holding-usd text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-500 to-indigo-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Cleared Payments</p><p class="text-3xl font-bold text-gray-900 mt-1">{{ $clearedCount }}</p><p class="text-[13px] text-gray-400 mt-2">Fully reconciled</p></div>
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-indigo-200 group-hover:scale-105 transition-transform"><i class="fas fa-check-circle text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-amber-400 to-orange-500"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Pending Clearance</p><p class="text-3xl font-bold text-gray-900 mt-1">{{ $pendingCount }}</p><p class="text-[13px] text-gray-400 mt-2">P{{ number_format($pendingAmount) }}{{ $pendingCustomer ? ' - ' . $pendingCustomer->customer->name : '' }}</p></div>
                    <div class="bg-gradient-to-br from-amber-400 to-orange-500 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-orange-200 group-hover:scale-105 transition-transform"><i class="fas fa-hourglass-half text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-purple-500 to-fuchsia-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Top Payment Method</p><p class="text-3xl font-bold text-[#4338ca] mt-1">{{ $topMethodLabel }}</p><p class="text-[13px] text-gray-400 mt-2">Most used this period</p></div>
                    <div class="bg-gradient-to-br from-purple-500 to-fuchsia-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-purple-200 group-hover:scale-105 transition-transform"><i class="fas fa-mobile-screen text-xl text-white"></i></div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 relative">
                <!-- Payments Table -->
                <div class="lg:col-span-3 bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
                    <div class="p-6 pb-4 flex items-center justify-between border-b border-gray-50">
                        <h3 class="font-bold text-gray-800 text-[16px] flex items-center gap-2"><i class="fas fa-receipt text-indigo-500"></i> Payments Received</h3>
                        <span class="text-[11px] font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full" x-text="payments.length + ' records'"></span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-[14px] text-left">
                            <thead class="bg-gradient-to-r from-slate-50 to-slate-100/70 text-gray-500">
                                <tr>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Reference</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Customer</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Date</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Method</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200 text-right">Amount</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Applied To</th>
                                    <th class="px-6 py-3 font-semibold text-[11px] uppercase tracking-wider border-b-2 border-gray-200">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(payment, idx) in payments" :key="payment.id">
                                    <tr class="transition-colors" :class="idx % 2 === 0 ? 'bg-white hover:bg-indigo-50/50' : 'bg-[#fafbfc] hover:bg-indigo-50/50'">
                                        <td class="px-6 py-3.5 font-medium text-gray-800" x-text="payment.ref"></td>
                                        <td class="px-6 py-3.5 text-gray-600" x-text="payment.customer"></td>
                                        <td class="px-6 py-3.5 text-gray-500" x-text="payment.date"></td>
                                        <td class="px-6 py-3.5 text-gray-700">
                                            <span class="inline-flex items-center gap-1.5">
                                                <i class="fas w-3.5 text-center" :class="{'fa-university text-blue-500': payment.method==='Bank Transfer', 'fa-mobile-screen text-fuchsia-500': payment.method==='Gcash', 'fa-money-check text-emerald-500': payment.method==='Check', 'fa-money-bill-wave text-yellow-500': payment.method==='Cash'}"></i>
                                                <span x-text="payment.method"></span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-3.5 font-medium text-gray-900 text-right tabular-nums" x-text="'₱' + Number(payment.amount).toLocaleString()"></td>
                                        <td class="px-6 py-3.5 text-[13px] text-gray-600" x-text="payment.applied"></td>
                                        <td class="px-6 py-3.5">
                                            <span class="px-3 py-1 text-[12px] font-medium rounded-full ring-1 ring-inset" 
                                                  :class="{'bg-[#f0fdf4] text-[#15803d] ring-green-200': payment.status === 'Cleared', 'bg-[#fffbeb] text-[#b45309] ring-amber-200': payment.status === 'Pending'}">
                                                <span x-text="payment.status"></span>
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="payments.length === 0">
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-400 text-[14px]">No payments recorded yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Method Breakdown -->
                <div class="bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 h-fit relative overflow-hidden">
                    <div class="absolute -top-8 -right-8 w-28 h-28 bg-indigo-50 rounded-full"></div>
                    <h3 class="font-bold text-gray-800 text-[15px] mb-4 flex items-center gap-2 relative"><i class="fas fa-chart-pie text-indigo-500"></i> Payment Method Breakdown</h3>
                    <div class="space-y-4 mb-4 relative">
                        <template x-for="method in methodBreakdown" :key="method.label">
                            <div class="flex items-center text-[13px]">
                                <span class="w-14 text-gray-600 font-medium" x-text="method.label"></span>
                                <div class="flex-1 h-3 bg-gray-100 rounded-full mx-3 overflow-hidden">
                                    <div class="h-full rounded-full transition-all ease-out"
                                         style="transition-duration: 1100ms;"
                                         :style="'width: ' + (barsLoaded ? method.pct : 0) + '%; background-color: ' + method.color"></div>
                                </div>
                                <span class="font-medium text-gray-800 tabular-nums" x-text="'₱' + Number(method.amount).toLocaleString()"></span>
                            </div>
                        </template>
                        <div x-show="methodBreakdown.length === 0" class="text-center text-gray-400 text-[13px] py-4">No payment data available.</div>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-between text-[14px] font-bold relative">
                        <span class="text-gray-800">Total Received</span><span class="text-[#4338ca]" x-text="'₱' + Number(totalReceived).toLocaleString()"></span>
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
            <form @submit.prevent="submitPayment">
            <div class="space-y-4 text-[14px]">
                <div>
                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Customer <span class="text-red-500">*</span></label>
                    <select x-model="form.customer_id" @change="updateInvoiceList" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]" required>
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Apply to Invoice <span class="text-red-500">*</span></label>
                    <select x-model="form.invoice_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]" required>
                        <option value="">-- Select Invoice --</option>
                        <template x-for="inv in filteredInvoices" :key="inv.id">
                            <option :value="inv.id" x-text="inv.invoice_number + ' - P' + Number(inv.total).toLocaleString() + ' (' + inv.status.charAt(0).toUpperCase() + inv.status.slice(1) + ')'"></option>
                        </template>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Amount Received <span class="text-red-500">*</span></label><input type="number" x-model="form.amount" step="0.01" min="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]" placeholder="0.00" required></div>
                    <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Payment Date <span class="text-red-500">*</span></label><input type="date" x-model="form.payment_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]" required></div>
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Payment Method <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-3">
                        <label class="flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-50 bg-[#f9fafb]"><input type="radio" name="method" value="bank_transfer" x-model="form.method"> <i class="fas fa-university text-[14px]"></i> Bank</label>
                        <label class="flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-50 bg-[#f9fafb]"><input type="radio" name="method" value="gcash" x-model="form.method"> <i class="fas fa-mobile-screen text-[14px]"></i> GCash</label>
                        <label class="flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-50 bg-[#f9fafb]"><input type="radio" name="method" value="check" x-model="form.method"> <i class="fas fa-money-check text-[14px]"></i> Check</label>
                        <label class="flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-50 bg-[#f9fafb]"><input type="radio" name="method" value="cash" x-model="form.method"> <i class="fas fa-money-bill-wave text-[14px]"></i> Cash</label>
                    </div>
                </div>
                <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Notes <span class="text-gray-400 font-normal">(Optional)</span></label><textarea x-model="form.notes" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none text-[14px] bg-[#f9fafb]" rows="2" placeholder="e.g. Partial Payment for June Delivery"></textarea></div>
            </div>
            <div class="mt-6 flex justify-end space-x-3 border-t pt-4">
                <button @click="showPaymentModal = false" type="button" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-600 text-[14px] hover:bg-gray-50 transition">Cancel</button>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-[#2563eb] to-[#4338ca] hover:brightness-110 text-white rounded-lg text-[14px] font-medium transition shadow-sm">Record Payment</button>
            </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function paymentApp() {
            return {
                showPaymentModal: false,
                barsLoaded: false,
                form: {
                    customer_id: '',
                    invoice_id: '',
                    amount: '',
                    payment_date: '{{ date("Y-m-d") }}',
                    method: 'bank_transfer',
                    notes: '',
                },
                payments: @json($payments),
                allInvoices: @json($invoices),
                methodBreakdown: @json($methodBreakdown),
                totalReceived: {{ $totalReceived }},
                get filteredInvoices() {
                    if (!this.form.customer_id) return [];
                    return this.allInvoices.filter(inv => inv.customer_id == this.form.customer_id);
                },
                init() {
                    setTimeout(() => { this.barsLoaded = true; }, 150);
                },
                updateInvoiceList() {
                    this.form.invoice_id = '';
                },
                submitPayment() {
                    if (!this.form.customer_id || !this.form.invoice_id || !this.form.amount || !this.form.method) {
                        alert('Please fill in all required fields.');
                        return;
                    }

                    fetch('{{ route("ar.payments.store") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(this.form),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            this.showPaymentModal = false;
                            const p = data.payment;
                            const methodLabels = { bank_transfer: 'Bank Transfer', gcash: 'Gcash', check: 'Check', cash: 'Cash' };
                            this.payments.unshift({
                                id: p.id,
                                ref: p.reference_no,
                                customer: p.customer.name,
                                date: new Date(p.payment_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
                                method: methodLabels[p.method] || 'Bank Transfer',
                                amount: Number(p.amount),
                                applied: p.applications.map(a => a.invoice.invoice_number + ' (Full)').join(', '),
                                status: 'Pending',
                            });
                            this.form = { customer_id: '', invoice_id: '', amount: '', payment_date: '{{ date("Y-m-d") }}', method: 'bank_transfer', notes: '' };
                            alert('Payment ' + p.reference_no + ' recorded successfully!');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(err => { alert('An error occurred. Please try again.'); });
                }
            }
        }
</script>
@endpush
