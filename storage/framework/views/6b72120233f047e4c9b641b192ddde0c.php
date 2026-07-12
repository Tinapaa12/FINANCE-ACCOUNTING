<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance and Accounting - A/R Overview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; } ::-webkit-scrollbar-track { background: #0f172a; } ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        .content-scroll::-webkit-scrollbar-track { background: #f1f5f9; } .content-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; }
        .bg-dot-grid { background-image: radial-gradient(circle, #cbd5e1 1px, transparent 1px); background-size: 22px 22px; }
    </style>
</head>
<body class="bg-[#f3f4f6] h-screen flex overflow-hidden" x-data="overviewApp()" x-cloak>

    <!-- SIDEBAR -->
    <aside class="w-64 bg-gradient-to-b from-[#0f172a] via-[#0f172a] to-[#0b1120] text-gray-300 flex flex-col h-full shrink-0 overflow-y-auto font-sans relative">
        <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="p-6 flex items-center space-x-3 border-b border-[#1e293b] relative">
            <div class="bg-gradient-to-br from-[#3b82f6] to-[#4338ca] p-2.5 rounded-lg text-white shadow-lg shadow-indigo-900/40"><i class="fas fa-coins text-xl"></i></div>
            <div><h1 class="text-white font-bold text-[15px] leading-tight">Finance and<br>Accounting</h1></div>
        </div>
        <nav class="flex-1 py-6 px-4 space-y-8 text-[13px] relative">
            <div>
                <div class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Main</div>
                <a href="#" class="flex items-center space-x-3 px-4 py-2.5 rounded-md hover:bg-[#1e293b] hover:text-white hover:pl-5 transition-all duration-200"><i class="fas fa-th-large w-5 text-center"></i><span>Dashboard</span></a>
            </div>
            <div>
                <div class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">General Ledger</div>
                <a href="#" class="flex items-center space-x-3 px-4 py-2.5 rounded-md hover:bg-[#1e293b] hover:text-white hover:pl-5 transition-all duration-200"><i class="fas fa-layer-group w-5 text-center"></i><span>Chart of Accounts</span></a>
                <a href="#" class="flex items-center space-x-3 px-4 py-2.5 rounded-md hover:bg-[#1e293b] hover:text-white hover:pl-5 transition-all duration-200"><i class="fas fa-book w-5 text-center"></i><span>Journal Entries</span></a>
            </div>
            <div>
                <div class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Account Payables</div>
                <a href="#" class="flex items-center space-x-3 px-4 py-2.5 rounded-md hover:bg-[#1e293b] hover:text-white hover:pl-5 transition-all duration-200"><i class="fas fa-file-invoice w-5 text-center"></i><span>Supplier Bills</span></a>
                <a href="#" class="flex items-center space-x-3 px-4 py-2.5 rounded-md hover:bg-[#1e293b] hover:text-white hover:pl-5 transition-all duration-200"><i class="fas fa-hand-holding-usd w-5 text-center"></i><span>Payments Made</span></a>
            </div>
            <div>
                <div class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Account Receivables</div>
                <a href="/ar-overview" class="flex items-center space-x-3 px-4 py-2.5 rounded-md bg-gradient-to-r from-[#4338ca] to-[#4f46e5] text-white font-medium shadow-md shadow-indigo-950/50 border-l-2 border-[#a5b4fc] transition"><i class="fas fa-file-invoice-dollar w-5 text-center"></i><span>A/R Overview</span></a>
                <a href="/payments-received" class="flex items-center space-x-3 px-4 py-2.5 rounded-md hover:bg-[#1e293b] hover:text-white hover:pl-5 transition-all duration-200"><i class="fas fa-hand-holding-heart w-5 text-center"></i><span>Payments Received</span></a>
            </div>
            <div>
                <div class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Reports</div>
                <a href="/aging-report" class="flex items-center space-x-3 px-4 py-2.5 rounded-md hover:bg-[#1e293b] hover:text-white hover:pl-5 transition-all duration-200"><i class="fas fa-file-alt w-5 text-center"></i><span>Financial Reports</span></a>
                <a href="#" class="flex items-center space-x-3 px-4 py-2.5 rounded-md hover:bg-[#1e293b] hover:text-white hover:pl-5 transition-all duration-200"><i class="fas fa-file-invoice w-5 text-center"></i><span>Tax and Compliance</span></a>
            </div>
        </nav>
        <div class="p-4 mx-4 mb-4 rounded-lg bg-white/5 border border-white/10 relative">
            <div class="flex items-center gap-2 text-[11px] text-slate-400"><span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_8px_2px_rgba(52,211,153,0.5)]"></span> All systems operational</div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 h-full flex flex-col overflow-hidden bg-[#f8fafc] relative">
        <header class="bg-white/90 backdrop-blur px-10 py-5 border-b border-gray-200 flex justify-between items-center shrink-0 shadow-sm relative z-10">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Account Receivable Overview</h2>
                <p class="text-[13px] text-gray-400 mt-0.5">Track invoices, collections, and customer balances</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-slate-800 to-black rounded-full w-10 h-10 flex items-center justify-center text-white shadow-md"><i class="fas fa-user text-xl"></i></div>
                <div class="text-[14px]">
                    <p class="font-bold text-gray-900">Admin User</p>
                    <p class="text-[12px] text-gray-500 -mt-0.5">Administrator</p>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-500"></div>
        </header>

        <div class="flex-1 overflow-y-auto content-scroll p-10 space-y-8 relative bg-dot-grid">
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-200/30 rounded-full blur-3xl pointer-events-none -z-0"></div>
            <div class="absolute top-96 left-0 w-72 h-72 bg-blue-200/20 rounded-full blur-3xl pointer-events-none -z-0"></div>

            <!-- 4 Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 relative">
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-500 to-indigo-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Total Outstanding</p><p class="text-3xl font-bold text-gray-900 mt-1">₱<?php echo e(number_format($totalOutstanding, 0)); ?></p><p class="text-[13px] text-gray-400 mt-2">Across <?php echo e($outstandingCount); ?> invoices</p></div>
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-indigo-200 group-hover:scale-105 transition-transform"><i class="fas fa-sack-dollar text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-rose-500 to-red-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Overdue Amount</p><p class="text-3xl font-bold text-gray-900 mt-1">₱<?php echo e(number_format($overdueAmount, 0)); ?></p><p class="text-[13px] text-gray-400 mt-2"><?php echo e($overdueCount); ?> invoices past due date</p></div>
                    <div class="bg-gradient-to-br from-rose-500 to-red-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-red-200 group-hover:scale-105 transition-transform"><i class="far fa-clock text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-500 to-green-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Collected this Month</p><p class="text-3xl font-bold text-gray-900 mt-1">₱<?php echo e(number_format($collectedThisMonth, 0)); ?></p><p class="text-[13px] text-gray-400 mt-2">from <?php echo e($collectedCount); ?> payments received</p></div>
                    <div class="bg-gradient-to-br from-emerald-500 to-green-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-green-200 group-hover:scale-105 transition-transform"><i class="fas fa-calendar-day text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-purple-500 to-fuchsia-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Avg. Days to Collect</p><p class="text-3xl font-bold text-gray-900 mt-1"><?php echo e($avgDaysToCollect); ?> DAYS</p><p class="text-[13px] text-gray-400 mt-2">DSO - target is 30days</p></div>
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
                                        <td class="px-6 py-3.5 font-medium text-gray-900 text-right tabular-nums" x-text="'₱' + activity.amount.toLocaleString()"></td>
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
                                <span class="text-[19px] font-bold text-gray-900">₱<?php echo e(number_format($totalOutstanding, 0)); ?></span>
                            </div>
                        </div>
                        <div class="w-full mt-6 space-y-2.5">
                            <template x-for="bucket in agingBuckets" :key="bucket.label">
                                <div class="flex items-center justify-between text-[13px]">
                                    <span class="flex items-center gap-2 text-gray-600"><span class="w-2.5 h-2.5 rounded-sm shrink-0" :style="'background-color:' + bucket.color"></span><span x-text="bucket.label"></span></span>
                                    <span class="font-medium text-gray-800 tabular-nums" x-text="'₱' + bucket.amount.toLocaleString()"></span>
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
                    <a href="/aging-report" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-[14px] py-2.5 px-5 rounded-md font-medium transition flex items-center justify-center gap-2"><i class="fas fa-chart-line text-indigo-500"></i> View Aging Report</a>
                </div>
                <div class="hidden md:flex items-center gap-2 text-[12px] text-gray-400"><i class="fas fa-circle-info"></i> Data refreshed a few moments ago</div>
            </div>
        </div>
    </main>

    <!-- Success Flash Message -->
    <div x-data="{ show: <?php echo e(session('success') ? 'true' : 'false'); ?> }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="fixed top-10 right-10 bg-white rounded-xl shadow-2xl border-l-[6px] border-emerald-500 p-5 z-[60] flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center text-white text-xl shadow-md"><i class="fas fa-check"></i></div>
        <div><h4 class="font-bold text-gray-900">Success!</h4><p class="text-[13px] text-gray-500"><?php echo e(session('success')); ?></p></div>
    </div>

    <!-- CREATE INVOICE MODAL -->
    <div x-show="showInvoiceModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" x-cloak @click.away="showInvoiceModal = false">
        <div class="bg-white w-full max-w-6xl max-h-[90vh] overflow-y-auto rounded-xl shadow-2xl p-6" @click.stop>
            <form method="POST" action="/ar-overview">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="subtotal" x-model="invoiceSubtotal">
                <input type="hidden" name="vat_amount" x-model="invoiceVat">
                <input type="hidden" name="total" x-model="invoiceTotal">
                <template x-for="(item, index) in invoiceItems" :key="index">
                    <div>
                        <input type="hidden" :name="'items['+index+'][description]'" x-model="item.desc">
                        <input type="hidden" :name="'items['+index+'][qty]'" x-model="item.qty">
                        <input type="hidden" :name="'items['+index+'][price]'" x-model="item.price">
                    </div>
                </template>
                <div class="flex justify-between items-center border-b pb-4 mb-4">
                    <div class="flex items-center gap-2">
                        <button type="button" @click="showInvoiceModal = false" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2"><i class="fas fa-check-square text-indigo-600"></i> Create New Invoice</h2>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="md:col-span-2 space-y-5">
                        <div class="grid grid-cols-2 gap-5">
                            <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Customer <span class="text-red-500">*</span></label>
                                <select name="customer_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-[14px] focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]">
                                    <option value="">-- Select Customer --</option>
                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Invoice Type <span class="text-red-500">*</span></label><select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-[14px] focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]"><option>Invoice (Standard)</option><option>Credit Note (Refund)</option></select></div>
                        </div>
                        <div class="grid grid-cols-2 gap-5">
                            <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Invoice Date <span class="text-red-500">*</span></label><input type="date" name="invoice_date" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-[14px] focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]"></div>
                            <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Due Date <span class="text-red-500">*</span></label><input type="date" name="due_date" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-[14px] focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]"></div>
                        </div>
                        <div class="mt-2 border border-gray-200 rounded-lg overflow-hidden bg-white">
                            <div class="bg-[#f9fafb] px-5 py-3 flex items-center justify-between border-b border-gray-200"><span class="font-bold text-gray-700 text-[15px] flex items-center gap-2"><i class="fas fa-box"></i> Items / Services</span></div>
                            <div class="p-5 space-y-4 bg-white">
                                <div class="flex flex-wrap md:flex-nowrap gap-3 items-end font-medium text-[13px] text-gray-500">
                                    <div class="flex-1 min-w-[120px]">Description</div><div class="w-16 text-center">Qty</div><div class="w-28 text-right">Unit Price</div><div class="w-16 text-center">VAT %</div><div class="w-32 text-right">Total</div>
                                </div>
                                <template x-for="(item, index) in invoiceItems" :key="index">
                                    <div class="flex flex-wrap md:flex-nowrap gap-3 items-end border-b border-gray-100 pb-4">
                                        <div class="flex-1 min-w-[120px]"><input type="text" x-model="item.desc" class="w-full border-b border-gray-300 py-1.5 text-[14px] focus:border-[#2563eb] outline-none bg-transparent" /></div>
                                        <div class="w-16"><input type="number" x-model="item.qty" @input="calculateInvoiceTotals()" min="1" class="w-full border-b border-gray-300 py-1.5 text-[14px] text-center focus:border-[#2563eb] outline-none bg-transparent" /></div>
                                        <div class="w-28"><input type="number" x-model="item.price" @input="calculateInvoiceTotals()" min="0" step="0.01" class="w-full border-b border-gray-300 py-1.5 text-[14px] text-right focus:border-[#2563eb] outline-none bg-transparent" placeholder="0.00" /></div>
                                        <div class="w-16 text-center text-[14px] text-gray-600" x-text="item.vat + '%'"></div>
                                        <div class="w-32 text-right font-medium text-gray-900 text-[14px]" x-text="'₱' + ((item.qty * item.price) * (1 + (item.vat/100))).toFixed(2)"></div>
                                        <button type="button" @click="if(invoiceItems.length > 1) invoiceItems.splice(index, 1); calculateInvoiceTotals();" class="text-red-400 hover:text-red-600 text-[14px] p-1"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </template>
                                <button type="button" @click="invoiceItems.push({desc: '', qty: 1, price: 0, vat: 12}); calculateInvoiceTotals();" class="text-[14px] text-[#2563eb] font-medium hover:underline flex items-center gap-2 mt-2"><i class="fas fa-plus-circle"></i> Add Line Item</button>
                                
                                <div class="mt-6 pt-4 border-t border-gray-200 flex flex-col items-end space-y-2 w-full md:w-1/2 ml-auto text-[14px]">
                                    <div class="flex justify-between w-full text-gray-600"><span>Subtotal</span><span class="font-medium text-gray-900" x-text="'₱'+invoiceSubtotal.toFixed(2)"></span></div>
                                    <div class="flex justify-between w-full text-gray-600"><span>VAT</span><span class="font-medium text-gray-900" x-text="'₱'+invoiceVat.toFixed(2)"></span></div>
                                    <div class="flex justify-between w-full font-bold text-gray-900 pt-3 border-t border-gray-200"><span>Total Due</span><span x-text="'₱'+invoiceTotal.toFixed(2)"></span></div>
                                </div>
                                
                                <div class="mt-4 flex space-x-4">
                                    <button type="submit" name="action" value="draft" class="px-6 py-2.5 bg-white border border-[#2563eb] text-[#2563eb] hover:bg-[#eff6ff] rounded-md text-[14px] font-medium transition">Save and Draft</button>
                                    <button type="submit" name="action" value="post" class="px-6 py-2.5 bg-gradient-to-r from-[#2563eb] to-[#4338ca] hover:brightness-110 text-white rounded-md text-[14px] font-medium transition shadow-sm">Post and Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sidebar All Invoices Widget (Inside Modal) -->
                    <div class="md:col-span-1 bg-[#fafbfc] rounded-xl p-4 border border-gray-100 h-fit">
                        <h4 class="font-bold text-gray-800 text-[15px] mb-3">All Invoices</h4>
                        <div class="space-y-3 text-[12px]">
                            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100 flex items-center justify-between">
                                <div><span class="font-bold text-gray-700">INV-0001</span> <span class="text-gray-500 block mt-0.5">ABC Trading</span></div>
                                <div><span class="text-gray-900 font-medium block text-right">P45,000</span><span class="text-[#dc2626] block text-right text-[10px] font-medium">Overdue</span></div>
                            </div>
                            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100 flex items-center justify-between">
                                <div><span class="font-bold text-gray-700">INV-0022</span> <span class="text-gray-500 block mt-0.5">Santos Ent.</span></div>
                                <div><span class="text-gray-900 font-medium block text-right">P38,500</span><span class="text-[#ca8a04] block text-right text-[10px] font-medium">Sent</span></div>
                            </div>
                            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100 flex items-center justify-between">
                                <div><span class="font-bold text-gray-700">INV-0023</span> <span class="text-gray-500 block mt-0.5">Cruz & Sons</span></div>
                                <div><span class="text-gray-900 font-medium block text-right">P12,000</span><span class="text-[#16a34a] block text-right text-[10px] font-medium">Cleared</span></div>
                            </div>
                            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100 flex items-center justify-between">
                                <div><span class="font-bold text-gray-700">INV-0024</span> <span class="text-gray-500 block mt-0.5">Reyes Corp</span></div>
                                <div><span class="text-gray-900 font-medium block text-right">P78,000</span><span class="text-[#2563eb] block text-right text-[10px] font-medium">Draft</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function overviewApp() {
            return {
                showInvoiceModal: false,
                barsLoaded: false,
                invoiceItems: [{ desc: 'WebDev Services', qty: 1, price: 25000, vat: 12 }],
                invoiceSubtotal: 25000, invoiceVat: 3000, invoiceTotal: 28000,
                activities: <?php echo json_encode($activities, 15, 512) ?>,
                agingBuckets: <?php echo json_encode($agingBuckets, 15, 512) ?>,
                init() {
                    // Trigger the aging bars to animate in shortly after the page mounts
                    setTimeout(() => { this.barsLoaded = true; }, 150);
                    this.$nextTick(() => this.initAgingChart());
                },
                initAgingChart() {
                    const ctx = document.getElementById('agingDonut');
                    if (!ctx || typeof Chart === 'undefined') return;
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: this.agingBuckets.map(b => b.label),
                            datasets: [{
                                data: this.agingBuckets.map(b => b.amount),
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
                }
            }
        }
    </script>
</body>
</html><?php /**PATH C:\laragon\www\finance-accounting\resources\views/ar-overview.blade.php ENDPATH**/ ?>