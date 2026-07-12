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
                    <div><p class="text-[13px] text-gray-500 font-medium">Total Outstanding</p><p class="text-3xl font-bold text-gray-900 mt-1">P{{ number_format($totalOutstanding) }}</p><p class="text-[13px] text-gray-400 mt-2">Across {{ $totalInvoices }} invoices</p></div>
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-indigo-200 group-hover:scale-105 transition-transform"><i class="fas fa-sack-dollar text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-rose-500 to-red-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Overdue Amount</p><p class="text-3xl font-bold text-gray-900 mt-1">P{{ number_format($overdueAmount) }}</p><p class="text-[13px] text-gray-400 mt-2">{{ $overdueCount }} invoices past due date</p></div>
                    <div class="bg-gradient-to-br from-rose-500 to-red-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-red-200 group-hover:scale-105 transition-transform"><i class="far fa-clock text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-500 to-green-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Collected this Month</p><p class="text-3xl font-bold text-gray-900 mt-1">P{{ number_format($collectedThisMonth) }}</p><p class="text-[13px] text-gray-400 mt-2">from {{ $paymentCount }} payments received</p></div>
                    <div class="bg-gradient-to-br from-emerald-500 to-green-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-green-200 group-hover:scale-105 transition-transform"><i class="fas fa-calendar-day text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-purple-500 to-fuchsia-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Avg. Days to Collect</p><p class="text-3xl font-bold text-gray-900 mt-1">24 DAYS</p><p class="text-[13px] text-gray-400 mt-2">DSO - target is 30days</p></div>
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
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(activity, idx) in activities" :key="activity.id">
                                    <tr class="transition-colors group" :class="idx % 2 === 0 ? 'bg-white hover:bg-indigo-50/50' : 'bg-[#fafbfc] hover:bg-indigo-50/50'">
                                        <td class="px-6 py-3.5 text-gray-600">
                                            <span class="inline-flex items-center gap-1.5">
                                                <i class="fas w-3.5 text-center" :class="{'fa-file-invoice text-blue-500': activity.type==='Invoice', 'fa-hand-holding-usd text-emerald-500': activity.type==='Payment', 'fa-triangle-exclamation text-red-500': activity.type==='Overdue'}"></i>
                                                <span x-text="activity.type"></span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-3.5 font-medium text-gray-900" x-text="activity.ref"></td>
                                        <td class="px-6 py-3.5 text-gray-600" x-text="activity.customer"></td>
                                        <td class="px-6 py-3.5 font-medium text-gray-900 text-right tabular-nums" x-text="'₱' + Number(activity.amount).toLocaleString()"></td>
                                        <td class="px-6 py-3.5 text-gray-500" x-text="activity.date"></td>
                                        <td class="px-6 py-3.5">
                                            <span class="px-3 py-1 text-[12px] font-medium rounded-full ring-1 ring-inset" 
                                                  :class="{'bg-[#fef9c3] text-[#a16207] ring-yellow-200': activity.status === 'Sent', 
                                                           'bg-[#f0fdf4] text-[#15803d] ring-green-200': activity.status === 'Cleared', 
                                                           'bg-[#eff6ff] text-[#1d4ed8] ring-blue-200': activity.status === 'Draft', 
                                                           'bg-[#fff7ed] text-[#c2410c] ring-orange-200': activity.status === 'Overdue'}">
                                                <span x-text="activity.status"></span>
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="activities.length === 0">
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-[14px]">No activities yet. Create your first invoice!</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Aging Summary -->
                <div class="bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 h-fit relative overflow-hidden">
                    <div class="absolute -top-8 -right-8 w-28 h-28 bg-indigo-50 rounded-full"></div>
                    <h3 class="font-bold text-gray-800 text-[16px] mb-5 flex items-center gap-2 relative"><i class="fas fa-layer-group text-indigo-500"></i> A/R Aging Summary</h3>
                    <div class="flex flex-col items-center relative">
                        <div class="relative w-[190px] h-[190px]">
                            <canvas id="agingDonut" role="img" aria-label="Donut chart of accounts receivable aging buckets"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-[11px] text-gray-400">Total</span>
                                <span class="text-[19px] font-bold text-gray-900" x-text="'₱' + agingTotal.toLocaleString()"></span>
                            </div>
                        </div>
                        <div class="w-full mt-6 space-y-2.5">
                            <template x-for="bucket in agingBuckets" :key="bucket.label">
                                <div class="flex items-center justify-between text-[13px]">
                                    <span class="flex items-center gap-2 text-gray-600"><span class="w-2.5 h-2.5 rounded-sm shrink-0" :style="'background-color:' + bucket.color"></span><span x-text="bucket.label"></span></span>
                                    <span class="font-medium text-gray-800 tabular-nums" x-text="'₱' + Number(bucket.amount).toLocaleString()"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="bg-gradient-to-r from-white to-slate-50 p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex items-center justify-between">
                <div class="flex space-x-4">
                    <button @click="showInvoiceModal = true" class="bg-gradient-to-r from-[#2563eb] to-[#4338ca] hover:brightness-110 text-white text-[14px] py-2.5 px-5 rounded-md font-medium transition flex items-center justify-center gap-2 shadow-md shadow-indigo-200"><i class="fas fa-plus"></i> New Invoice</button>
                    <a href="{{ route('ar.aging') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-[14px] py-2.5 px-5 rounded-md font-medium transition flex items-center justify-center gap-2"><i class="fas fa-chart-line text-indigo-500"></i> View Aging Report</a>
                </div>
                <div class="hidden md:flex items-center gap-2 text-[12px] text-gray-400"><i class="fas fa-circle-info"></i> Data refreshed a few moments ago</div>
            </div>
        </div>

<!-- CREATE INVOICE MODAL -->
    <div x-show="showInvoiceModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" x-cloak @click.away="showInvoiceModal = false">
        <div class="bg-white w-full max-w-6xl max-h-[90vh] overflow-y-auto rounded-xl shadow-2xl p-6" @click.stop>
            <div class="flex justify-between items-center border-b pb-4 mb-4">
                <div class="flex items-center gap-2">
                    <button @click="showInvoiceModal = false" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2"><i class="fas fa-check-square text-indigo-600"></i> Create New Invoice</h2>
                </div>
            </div>
            <form @submit.prevent="submitInvoice">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="md:col-span-2 space-y-5">
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Customer <span class="text-red-500">*</span></label>
                            <select x-model="form.customer_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-[14px] focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]" required>
                                <option value="">Select customer...</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Invoice Type <span class="text-red-500">*</span></label>
                            <select x-model="form.type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-[14px] focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]">
                                <option value="invoice">Invoice (Standard)</option>
                                <option value="credit_note">Credit Note (Refund)</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-5">
                        <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Invoice Date <span class="text-red-500">*</span></label><input type="date" x-model="form.invoice_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-[14px] focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]" required></div>
                        <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Due Date <span class="text-red-500">*</span></label><input type="date" x-model="form.due_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-[14px] focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]" required></div>
                        <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Currency <span class="text-red-500">*</span></label>
                            <select x-model="form.currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-[14px] focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]">
                                <option value="PHP">PHP - Philippine Peso</option>
                                <option value="USD">USD</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-2 border border-gray-200 rounded-lg overflow-hidden bg-white">
                        <div class="bg-[#f9fafb] px-5 py-3 flex items-center justify-between border-b border-gray-200"><span class="font-bold text-gray-700 text-[15px] flex items-center gap-2"><i class="fas fa-box"></i> Items / Services</span></div>
                        <div class="p-5 space-y-4 bg-white">
                            <div class="flex flex-wrap md:flex-nowrap gap-3 items-end font-medium text-[13px] text-gray-500">
                                <div class="flex-1 min-w-[120px]">Description</div><div class="w-16 text-center">Qty</div><div class="w-28 text-right">Unit Price</div><div class="w-16 text-center">VAT %</div><div class="w-32 text-right">Total</div>
                            </div>
                            <template x-for="(item, index) in invoiceItems" :key="index">
                                <div class="flex flex-wrap md:flex-nowrap gap-3 items-end border-b border-gray-100 pb-4">
                                    <div class="flex-1 min-w-[120px]"><input type="text" x-model="item.desc" class="w-full border-b border-gray-300 py-1.5 text-[14px] focus:border-[#2563eb] outline-none bg-transparent" placeholder="Item description" required /></div>
                                    <div class="w-16"><input type="number" x-model="item.qty" @input="calculateInvoiceTotals()" min="1" class="w-full border-b border-gray-300 py-1.5 text-[14px] text-center focus:border-[#2563eb] outline-none bg-transparent" required /></div>
                                    <div class="w-28"><input type="number" x-model="item.price" @input="calculateInvoiceTotals()" min="0" step="0.01" class="w-full border-b border-gray-300 py-1.5 text-[14px] text-right focus:border-[#2563eb] outline-none bg-transparent" placeholder="0.00" required /></div>
                                    <div class="w-16 text-center text-[14px] text-gray-600" x-text="item.vat + '%'"></div>
                                    <div class="w-32 text-right font-medium text-gray-900 text-[14px]" x-text="'₱' + ((item.qty * item.price) * (1 + (item.vat/100))).toFixed(2)"></div>
                                    <button @click="if(invoiceItems.length > 1) invoiceItems.splice(index, 1); calculateInvoiceTotals();" type="button" class="text-red-400 hover:text-red-600 text-[14px] p-1"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </template>
                            <button @click="invoiceItems.push({desc: '', qty: 1, price: 0, vat: 12}); calculateInvoiceTotals();" type="button" class="text-[14px] text-[#2563eb] font-medium hover:underline flex items-center gap-2 mt-2"><i class="fas fa-plus-circle"></i> Add Line Item</button>
                            
                            <div class="mt-6 pt-4 border-t border-gray-200 flex flex-col items-end space-y-2 w-full md:w-1/2 ml-auto text-[14px]">
                                <div class="flex justify-between w-full text-gray-600"><span>Subtotal</span><span class="font-medium text-gray-900" x-text="'₱'+invoiceSubtotal.toFixed(2)"></span></div>
                                <div class="flex justify-between w-full text-gray-600"><span>VAT</span><span class="font-medium text-gray-900" x-text="'₱'+invoiceVat.toFixed(2)"></span></div>
                                <div class="flex justify-between w-full font-bold text-gray-900 pt-3 border-t border-gray-200"><span>Total Due</span><span x-text="'₱'+invoiceTotal.toFixed(2)"></span></div>
                            </div>
                            
                            <div class="mt-4 flex space-x-4">
                                <button type="submit" class="px-6 py-2.5 bg-white border border-[#2563eb] text-[#2563eb] hover:bg-[#eff6ff] rounded-md text-[14px] font-medium transition">Save as Draft</button>
                                <button type="button" @click="submitInvoice('sent')" class="px-6 py-2.5 bg-gradient-to-r from-[#2563eb] to-[#4338ca] hover:brightness-110 text-white rounded-md text-[14px] font-medium transition shadow-sm">Post and Send</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar All Invoices Widget (Inside Modal) -->
                <div class="md:col-span-1 bg-[#fafbfc] rounded-xl p-4 border border-gray-100 h-fit">
                    <h4 class="font-bold text-gray-800 text-[15px] mb-3">All Invoices</h4>
                    <div class="space-y-3 text-[12px]">
                        <template x-for="inv in recentInvoices" :key="inv.id">
                            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100 flex items-center justify-between">
                                <div><span class="font-bold text-gray-700" x-text="inv.invoice_number"></span> <span class="text-gray-500 block mt-0.5" x-text="inv.customer?.name || 'Unknown'"></span></div>
                                <div><span class="text-gray-900 font-medium block text-right" x-text="'P' + Number(inv.total).toLocaleString()"></span><span class="block text-right text-[10px] font-medium" :class="{'text-red-600': inv.status==='overdue', 'text-yellow-600': inv.status==='sent', 'text-green-600': inv.status==='cleared', 'text-blue-600': inv.status==='draft'}" x-text="inv.status.charAt(0).toUpperCase() + inv.status.slice(1)"></span></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function overviewApp() {
            return {
                showInvoiceModal: false,
                barsLoaded: false,
                form: {
                    customer_id: '',
                    type: 'invoice',
                    invoice_date: '{{ date("Y-m-d") }}',
                    due_date: '{{ date("Y-m-d", strtotime("+30 days")) }}',
                    currency: 'PHP',
                },
                invoiceItems: [{ desc: '', qty: 1, price: 0, vat: 12 }],
                invoiceSubtotal: 0, invoiceVat: 0, invoiceTotal: 0,
                activities: @json($activities),
                agingBuckets: @json($agingBuckets),
                recentInvoices: [],
                get agingTotal() {
                    return this.agingBuckets.reduce((sum, b) => sum + Number(b.amount), 0);
                },
                init() {
                    setTimeout(() => { this.barsLoaded = true; }, 150);
                    this.$nextTick(() => this.initAgingChart());
                    this.fetchRecentInvoices();
                },
                fetchRecentInvoices() {
                    fetch('{{ url("ar/invoices/recent") }}')
                        .then(r => r.json())
                        .then(data => { this.recentInvoices = data; })
                        .catch(() => {});
                },
                initAgingChart() {
                    const ctx = document.getElementById('agingDonut');
                    if (!ctx || typeof Chart === 'undefined') return;
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: this.agingBuckets.map(b => b.label),
                            datasets: [{
                                data: this.agingBuckets.map(b => Number(b.amount)),
                                backgroundColor: this.agingBuckets.map(b => b.color),
                                borderColor: '#ffffff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { label: (c) => c.label + ': ₱' + c.parsed.toLocaleString() } }
                            }
                        }
                    });
                },
                calculateInvoiceTotals() {
                    let sub = 0; this.invoiceItems.forEach(item => { sub += item.qty * item.price; });
                    this.invoiceSubtotal = sub; this.invoiceVat = sub * 0.12; this.invoiceTotal = sub + this.invoiceVat;
                },
                submitInvoice(status = 'draft') {
                    if (!this.form.customer_id) { alert('Please select a customer.'); return; }

                    const payload = {
                        ...this.form,
                        subtotal: this.invoiceSubtotal,
                        vat_amount: this.invoiceVat,
                        total: this.invoiceTotal,
                        status: status,
                        items: this.invoiceItems.map(item => ({
                            description: item.desc,
                            qty: item.qty,
                            unit_price: item.price,
                            vat_percent: item.vat,
                            line_total: item.qty * item.price * (1 + item.vat / 100),
                        })),
                    };

                    fetch('{{ route("ar.invoices.store") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(payload),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            this.showInvoiceModal = false;
                            this.activities.unshift({
                                id: data.invoice.id,
                                type: data.invoice.type === 'credit_note' ? 'Credit Note' : 'Invoice',
                                ref: data.invoice.invoice_number,
                                customer: data.invoice.customer.name,
                                amount: Number(data.invoice.total),
                                date: new Date(data.invoice.invoice_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
                                status: data.invoice.status.charAt(0).toUpperCase() + data.invoice.status.slice(1),
                            });
                            this.fetchRecentInvoices();
                            alert('Invoice ' + data.invoice.invoice_number + ' created successfully!');
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
