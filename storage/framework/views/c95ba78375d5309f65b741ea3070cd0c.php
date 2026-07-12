<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance and Accounting - Payments Received</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; } ::-webkit-scrollbar-track { background: #0f172a; } ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        .content-scroll::-webkit-scrollbar-track { background: #f1f5f9; } .content-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; }
        .bg-dot-grid { background-image: radial-gradient(circle, #cbd5e1 1px, transparent 1px); background-size: 22px 22px; }
    </style>
</head>
<body class="bg-[#f3f4f6] h-screen flex overflow-hidden" x-data="paymentApp()" x-cloak>
    
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
                <a href="/ar-overview" class="flex items-center space-x-3 px-4 py-2.5 rounded-md hover:bg-[#1e293b] hover:text-white hover:pl-5 transition-all duration-200"><i class="fas fa-file-invoice-dollar w-5 text-center"></i><span>A/R Overview</span></a>
                <a href="/payments-received" class="flex items-center space-x-3 px-4 py-2.5 rounded-md bg-gradient-to-r from-[#4338ca] to-[#4f46e5] text-white font-medium shadow-md shadow-indigo-950/50 border-l-2 border-[#a5b4fc] transition"><i class="fas fa-hand-holding-heart w-5 text-center"></i><span>Payments Received</span></a>
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

    <main class="flex-1 h-full flex flex-col overflow-hidden bg-[#f8fafc] relative">
        <header class="bg-white/90 backdrop-blur px-10 py-5 border-b border-gray-200 flex justify-between items-center shrink-0 shadow-sm relative z-10">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Accounts Receivable</h2>
                <p class="text-[13px] text-gray-400 mt-0.5">Reconcile and track customer payments</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-slate-800 to-black rounded-full w-10 h-10 flex items-center justify-center text-white shadow-md"><i class="fas fa-user text-xl"></i></div>
                <div class="text-[14px]"><p class="font-bold text-gray-900">Admin User</p><p class="text-[12px] text-gray-500 -mt-0.5">Administrator</p></div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-500"></div>
        </header>

        <div class="flex-1 overflow-y-auto content-scroll p-10 space-y-8 relative bg-dot-grid">
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-200/30 rounded-full blur-3xl pointer-events-none -z-0"></div>
            <div class="absolute top-96 left-0 w-72 h-72 bg-emerald-200/20 rounded-full blur-3xl pointer-events-none -z-0"></div>

            <div class="flex justify-between items-center relative">
                <a href="/ar-overview" class="text-[14px] text-gray-500 hover:text-[#2563eb] font-medium flex items-center gap-2"><i class="fas fa-arrow-left"></i> Back to A/R Overview</a>
                <button @click="showPaymentModal = true" class="bg-gradient-to-r from-[#2563eb] to-[#4338ca] hover:brightness-110 text-white text-[14px] font-medium py-2.5 px-5 rounded-md transition shadow-md shadow-indigo-200 flex items-center gap-2"><i class="fas fa-plus"></i> Record Payment</button>
            </div>

            <!-- 4 Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 relative">
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-500 to-green-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Total Collected (This Month)</p><p class="text-3xl font-bold text-gray-900 mt-1">₱<?php echo e(number_format($totalCollectedThisMonth, 0)); ?></p><p class="text-[13px] text-gray-400 mt-2">From <?php echo e($totalPaymentsCount); ?> payments</p></div>
                    <div class="bg-gradient-to-br from-emerald-500 to-green-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-green-200 group-hover:scale-105 transition-transform"><i class="fas fa-hand-holding-usd text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-500 to-indigo-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Cleared Payments</p><p class="text-3xl font-bold text-gray-900 mt-1"><?php echo e($clearedCount); ?></p><p class="text-[13px] text-gray-400 mt-2">Fully reconciled</p></div>
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-indigo-200 group-hover:scale-105 transition-transform"><i class="fas fa-check-circle text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-amber-400 to-orange-500"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Pending Clearance</p><p class="text-3xl font-bold text-gray-900 mt-1"><?php echo e($pendingCount); ?></p><p class="text-[13px] text-gray-400 mt-2"><?php if($pendingPayment): ?>₱<?php echo e(number_format($pendingPayment->amount, 0)); ?> - <?php echo e($pendingPayment->customer->name); ?><?php else: ?> No pending payments <?php endif; ?></p></div>
                    <div class="bg-gradient-to-br from-amber-400 to-orange-500 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-orange-200 group-hover:scale-105 transition-transform"><i class="fas fa-hourglass-half text-xl text-white"></i></div>
                </div>
                <div class="group bg-white p-6 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 flex justify-between items-start relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-purple-500 to-fuchsia-600"></div>
                    <div><p class="text-[13px] text-gray-500 font-medium">Top Payment Method</p><p class="text-3xl font-bold text-[#4338ca] mt-1"><?php echo e($topMethodLabel); ?></p><p class="text-[13px] text-gray-400 mt-2">Used in <?php echo e($topMethodCount); ?> of <?php echo e($totalPaymentsCount); ?> payments</p></div>
                    <div class="bg-gradient-to-br from-purple-500 to-fuchsia-600 p-3 rounded-full flex items-center justify-center w-12 h-12 shadow-md shadow-purple-200 group-hover:scale-105 transition-transform"><i class="fas fa-mobile-screen text-xl text-white"></i></div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 relative">
                <!-- Payments Table -->
                <div class="lg:col-span-3 bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
                    <div class="p-6 pb-4 flex items-center justify-between border-b border-gray-50">
                        <h3 class="font-bold text-gray-800 text-[16px] flex items-center gap-2"><i class="fas fa-receipt text-indigo-500"></i> Payments Received</h3>
                        <span class="text-[11px] font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full" x-text="paymentList.length + ' records'"></span>
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
                                <template x-for="(payment, idx) in paymentList" :key="payment.id">
                                    <tr class="transition-colors" :class="idx % 2 === 0 ? 'bg-white hover:bg-indigo-50/50' : 'bg-[#fafbfc] hover:bg-indigo-50/50'">
                                        <td class="px-6 py-3.5 font-medium text-gray-800" x-text="payment.ref"></td>
                                        <td class="px-6 py-3.5 text-gray-600" x-text="payment.customer"></td>
                                        <td class="px-6 py-3.5 text-gray-500" x-text="payment.date"></td>
                                        <td class="px-6 py-3.5 text-gray-700">
                                            <span class="inline-flex items-center gap-1.5">
                                                <i class="fas w-3.5 text-center" :class="{'fa-university text-blue-500': payment.method==='Bank Transfer', 'fa-mobile-screen text-fuchsia-500': payment.method==='GCash', 'fa-money-check text-emerald-500': payment.method==='Check'}"></i>
                                                <span x-text="payment.method"></span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-3.5 font-medium text-gray-900 text-right tabular-nums" x-text="'₱'+payment.amount.toLocaleString()"></td>
                                        <td class="px-6 py-3.5 text-[13px] text-gray-600" x-text="payment.applied"></td>
                                        <td class="px-6 py-3.5">
                                            <span class="px-3 py-1 text-[12px] font-medium rounded-full ring-1 ring-inset" 
                                                  :class="{'bg-[#f0fdf4] text-[#15803d] ring-green-200': payment.status === 'Cleared', 'bg-[#fffbeb] text-[#b45309] ring-amber-200': payment.status === 'Pending'}">
                                                <span x-text="payment.status"></span>
                                            </span>
                                        </td>
                                    </tr>
                                </template>
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
                                <span class="font-medium text-gray-800 tabular-nums" x-text="'₱' + method.amount.toLocaleString()"></span>
                            </div>
                        </template>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-between text-[14px] font-bold relative">
                        <span class="text-gray-800">Total Received</span><span class="text-[#4338ca]">₱<?php echo e(number_format($totalReceived, 0)); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Success Flash Message -->
    <div x-data="{ show: <?php echo e(session('success') ? 'true' : 'false'); ?> }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="fixed top-10 right-10 bg-white rounded-xl shadow-2xl border-l-[6px] border-emerald-500 p-5 z-[60] flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center text-white text-xl shadow-md"><i class="fas fa-check"></i></div>
        <div><h4 class="font-bold text-gray-900">Success!</h4><p class="text-[13px] text-gray-500"><?php echo e(session('success')); ?></p></div>
    </div>

    <!-- RECORD PAYMENT MODAL -->
    <div x-show="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" x-cloak @click.away="showPaymentModal = false">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl p-6" @click.stop>
            <form method="POST" action="/payments-received">
                <?php echo csrf_field(); ?>
                <div class="flex items-center gap-3 border-b pb-4 mb-4">
                    <i class="fas fa-hand-holding-usd text-xl text-white bg-gradient-to-br from-[#2563eb] to-[#4338ca] p-2 rounded-lg"></i>
                    <h2 class="text-lg font-bold text-gray-800">Record Payment</h2>
                    <button type="button" @click="showPaymentModal = false" class="ml-auto text-gray-400 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                </div>
                <div class="space-y-4 text-[14px]">
                    <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Customer <span class="text-red-500">*</span></label>
                        <select name="customer_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]">
                            <option value="">-- Select Customer --</option>
                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Apply to Invoice <span class="text-red-500">*</span></label>
                        <select name="invoice_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]">
                            <option value="">-- Select Invoice --</option>
                            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($inv->id); ?>"><?php echo e($inv->invoice_number); ?> - ₱<?php echo e(number_format($inv->total, 0)); ?> (<?php echo e(ucfirst($inv->status)); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Amount Received <span class="text-red-500">*</span></label><input type="number" name="amount" step="0.01" min="0.01" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]" placeholder="0.00"></div>
                        <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Payment Date <span class="text-red-500">*</span></label><input type="date" name="payment_date" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none bg-[#f9fafb]"></div>
                    </div>
                    <div>
                        <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Payment Method <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-3">
                            <label class="flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-50 bg-[#f9fafb]"><input type="radio" name="method" value="bank_transfer" checked> <i class="fas fa-university text-[14px]"></i> Bank</label>
                            <label class="flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-50 bg-[#f9fafb]"><input type="radio" name="method" value="gcash"> <i class="fas fa-mobile-screen text-[14px]"></i> GCash</label>
                            <label class="flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-50 bg-[#f9fafb]"><input type="radio" name="method" value="check"> <i class="fas fa-money-check text-[14px]"></i> Check</label>
                            <label class="flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-50 bg-[#f9fafb]"><input type="radio" name="method" value="cash"> <i class="fas fa-money-bill-wave text-[14px]"></i> Cash</label>
                        </div>
                    </div>
                    <div><label class="block text-[13px] font-medium text-gray-700 mb-1.5">Notes <span class="text-gray-400 font-normal">(Optional)</span></label><textarea name="notes" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#2563eb] outline-none text-[14px] bg-[#f9fafb]" rows="2" placeholder="e.g. Partial Payment for June Delivery"></textarea></div>
                </div>
                <div class="mt-6 flex justify-end space-x-3 border-t pt-4">
                    <button type="button" @click="showPaymentModal = false" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-600 text-[14px] hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-[#2563eb] to-[#4338ca] hover:brightness-110 text-white rounded-lg text-[14px] font-medium transition shadow-sm">Record</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function paymentApp() {
            return {
                showPaymentModal: false,
                barsLoaded: false,
                paymentList: <?php echo json_encode($paymentList, 15, 512) ?>,
                methodBreakdown: <?php echo json_encode($methodBreakdown, 15, 512) ?>,
                init() {
                    setTimeout(() => { this.barsLoaded = true; }, 150);
                }
            }
        }
    </script>
</body>
</html><?php /**PATH C:\laragon\www\finance-accounting\resources\views/payments-received.blade.php ENDPATH**/ ?>