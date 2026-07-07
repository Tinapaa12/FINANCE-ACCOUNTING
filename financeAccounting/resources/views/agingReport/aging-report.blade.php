<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance and Accounting - Aging Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; } ::-webkit-scrollbar-track { background: #0f172a; } ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        .content-scroll::-webkit-scrollbar-track { background: #f1f5f9; } .content-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; }
        .bg-dot-grid { background-image: radial-gradient(circle, #cbd5e1 1px, transparent 1px); background-size: 22px 22px; }
    </style>
</head>
<body class="bg-[#f3f4f6] h-screen flex overflow-hidden" x-data="reportApp()" x-cloak>
    
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
                <a href="/ar-overview" class="flex items-center space-x-3 px-4 py-2.5 rounded-md hover:bg-[#1e293b] hover:text-white hover:pl-5 transition-all duration-200"><i class="fas fa-th-large w-5 text-center"></i><span>Dashboard</span></a>
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
                <!-- FIX: Aging Report is a sub-view of A/R, so this stays highlighted as active instead of "Financial Reports" below -->
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

    <main class="flex-1 h-full flex flex-col overflow-hidden bg-[#f8fafc] relative">
        <header class="bg-white/90 backdrop-blur px-10 py-5 border-b border-gray-200 flex justify-between items-center shrink-0 shadow-sm relative z-10">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Accounts Receivable</h2>
                <p class="text-[13px] text-gray-400 mt-0.5">Aging breakdown by customer and days outstanding</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-slate-800 to-black rounded-full w-10 h-10 flex items-center justify-center text-white shadow-md"><i class="fas fa-user text-xl"></i></div>
                <div class="text-[14px]"><p class="font-bold text-gray-900">Admin User</p><p class="text-[12px] text-gray-500 -mt-0.5">Administrator</p></div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-500"></div>
        </header>

        <div class="flex-1 overflow-y-auto content-scroll p-10 space-y-6 relative bg-dot-grid">
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-200/30 rounded-full blur-3xl pointer-events-none -z-0"></div>
            <div class="absolute top-96 left-0 w-72 h-72 bg-rose-200/20 rounded-full blur-3xl pointer-events-none -z-0"></div>

            <!-- Top navigation & Filters -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative">
                <div class="flex items-center gap-6">
                    <a href="/ar-overview" class="text-[14px] text-gray-500 hover:text-[#2563eb] font-medium flex items-center gap-2"><i class="fas fa-arrow-left"></i> Back to A/R Overview</a>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-chart-line text-indigo-500"></i> A/R Aging Report</h3>
                </div>
                <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-lg px-3 py-1.5 shadow-sm">
                    <label for="overdueFilter" class="text-[13px] font-medium text-gray-600">Filter:</label>
                    <select x-model="overdueFilter" class="text-[14px] bg-transparent outline-none cursor-pointer text-gray-700 font-medium">
                        <option value="all">All Customers</option>
                        <option value="overdue">Overdue Only</option>
                    </select>
                </div>
            </div>

            <!-- 4 Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-emerald-400 to-[#22c55e]"></div>
                    <div class="flex items-center justify-between">
                        <p class="text-[14px] font-semibold text-[#16a34a]">Current (Not Due)</p>
                        <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center"><i class="fas fa-check text-[#16a34a] text-[12px]"></i></div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 mt-1">P98,000</p>
                    <p class="text-[13px] text-gray-500">4 Invoices</p>
                    <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-emerald-400 to-[#22c55e] rounded-full transition-all ease-out" style="transition-duration: 1100ms;" :style="'width: ' + (barsLoaded ? 46 : 0) + '%'"></div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-red-300 to-[#fca5a5]"></div>
                    <div class="flex items-center justify-between">
                        <p class="text-[14px] font-semibold text-[#ef4444]">1 - 30 Days Overdue</p>
                        <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center"><i class="fas fa-hourglass-start text-[#ef4444] text-[12px]"></i></div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 mt-1">P57,000</p>
                    <p class="text-[13px] text-gray-500">3 Invoices</p>
                    <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-red-300 to-[#ef4444] rounded-full transition-all ease-out" style="transition-duration: 1100ms;" :style="'width: ' + (barsLoaded ? 27 : 0) + '%'"></div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-red-400 to-[#f87171]"></div>
                    <div class="flex items-center justify-between">
                        <p class="text-[14px] font-semibold text-[#dc2626]">31 - 60 Days Overdue</p>
                        <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center"><i class="fas fa-hourglass-half text-[#dc2626] text-[12px]"></i></div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 mt-1">P32,000</p>
                    <p class="text-[13px] text-gray-500">2 Invoices</p>
                    <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-red-400 to-[#dc2626] rounded-full transition-all ease-out" style="transition-duration: 1100ms;" :style="'width: ' + (barsLoaded ? 15 : 0) + '%'"></div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-red-700 to-[#b91c1c]"></div>
                    <div class="flex items-center justify-between">
                        <p class="text-[14px] font-semibold text-[#b91c1c]">61+ Days Overdue</p>
                        <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center"><i class="fas fa-triangle-exclamation text-[#b91c1c] text-[12px]"></i></div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 mt-1">P25,500</p>
                    <p class="text-[13px] text-gray-500">3 invoices - urgent</p>
                    <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-red-700 to-[#b91c1c] rounded-full transition-all ease-out" style="transition-duration: 1100ms;" :style="'width: ' + (barsLoaded ? 12 : 0) + '%'"></div>
                    </div>
                </div>
            </div>

            <!-- Detailed Aging Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
                <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-slate-50 to-white flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-[15px] flex items-center gap-2"><i class="fas fa-table-list text-indigo-500"></i> Detailed Aging by Customer</h3>
                    <span class="text-[11px] font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full" x-text="filteredAgingData.length + ' customers'"></span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-[14px] text-left">
                        <thead class="bg-gradient-to-r from-slate-50 to-slate-100/70 text-[11px] font-semibold border-b-2 border-gray-200 text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3 text-left">Customer</th>
                                <th class="px-5 py-3 text-center text-[#16a34a]">Current</th>
                                <th class="px-5 py-3 text-center text-[#ef4444]">1-30 Days</th>
                                <th class="px-5 py-3 text-center text-[#dc2626]">30-60 Days</th>
                                <th class="px-5 py-3 text-center text-[#b91c1c]">61-90 Days</th>
                                <th class="px-5 py-3 text-center text-[#991b1b]">91+ Days</th>
                                <th class="px-5 py-3 text-center">Total</th>
                                <th class="px-5 py-3 text-center">Risk</th>
                                <th class="px-5 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(row, idx) in filteredAgingData" :key="row.customer">
                                <tr class="transition-colors" :class="idx % 2 === 0 ? 'bg-white hover:bg-indigo-50/50' : 'bg-[#fafbfc] hover:bg-indigo-50/50'">
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-gray-800" x-text="row.customer"></p>
                                        <p class="text-[12px] text-gray-400" x-text="row.email"></p>
                                    </td>
                                    <td class="px-5 py-4 text-center" :class="row.current ? 'text-[#16a34a] font-medium' : 'text-gray-300'" x-text="row.current ? 'P'+row.current.toLocaleString() : '-'"></td>
                                    <td class="px-5 py-4 text-center" :class="row.d1_30 ? 'text-[#ef4444] font-medium' : 'text-gray-300'" x-text="row.d1_30 ? 'P'+row.d1_30.toLocaleString() : '-'"></td>
                                    <td class="px-5 py-4 text-center" :class="row.d31_60 ? 'text-[#dc2626] font-medium' : 'text-gray-300'" x-text="row.d31_60 ? 'P'+row.d31_60.toLocaleString() : '-'"></td>
                                    <td class="px-5 py-4 text-center" :class="row.d61_90 ? 'text-[#b91c1c] font-medium' : 'text-gray-300'" x-text="row.d61_90 ? 'P'+row.d61_90.toLocaleString() : '-'"></td>
                                    <td class="px-5 py-4 text-center" :class="row.d90 ? 'text-[#991b1b] font-medium' : 'text-gray-300'" x-text="row.d90 ? 'P'+row.d90.toLocaleString() : '-'"></td>
                                    <td class="px-5 py-4 text-center font-bold text-gray-900" x-text="'P' + (row.current+row.d1_30+row.d31_60+row.d61_90+row.d90).toLocaleString()"></td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-[12px] font-medium ring-1 ring-inset" 
                                              :class="{'bg-[#dcfce7] text-[#16a34a] ring-green-200': row.risk === 'Low', 
                                                       'bg-[#fef9c3] text-[#a16207] ring-yellow-200': row.risk === 'Medium', 
                                                       'bg-[#fee2e2] text-[#dc2626] ring-red-200': row.risk === 'High'}">
                                            <span x-text="row.risk"></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <button @click="showReminderToast(row.customer)" class="bg-gradient-to-r from-[#2563eb] to-[#4338ca] hover:brightness-110 text-white text-[12px] font-medium px-4 py-1.5 rounded-full transition shadow-sm">Remind</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-gradient-to-r from-slate-50 to-slate-100/70 border-t-2 border-gray-200 font-bold text-gray-800 text-[14px]">
                            <tr>
                                <td class="px-5 py-4">Total</td>
                                <td class="px-5 py-4 text-center text-[#16a34a]">P98,000</td>
                                <td class="px-5 py-4 text-center text-[#ef4444]">P57,000</td>
                                <td class="px-5 py-4 text-center text-[#dc2626]">P32,000</td>
                                <td class="px-5 py-4 text-center text-[#b91c1c]">P16,000</td>
                                <td class="px-5 py-4 text-center text-[#991b1b]">P9,500</td>
                                <td class="px-5 py-4 text-center text-[#4338ca]">P212,500</td>
                                <td></td><td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- TOAST: Reminder Sent -->
    <div x-show="showReminderToastFlag" x-transition.duration.300ms class="fixed top-10 right-10 bg-white rounded-xl shadow-2xl border-l-[6px] border-[#2563eb] p-5 w-[280px] flex items-center gap-4 z-[60]" x-cloak>
        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl shadow-md"><i class="fas fa-check"></i></div>
        <div>
            <h4 class="font-bold text-gray-900 text-[15px]">Reminder Sent!</h4>
            <p class="text-[13px] text-gray-500" x-text="'to ' + lastRemindedCustomer"></p>
        </div>
    </div>

    <script>
        function reportApp() {
            return {
                showReminderToastFlag: false,
                lastRemindedCustomer: '',
                overdueFilter: 'all',
                barsLoaded: false,
                agingData: [
                    { customer: 'ABC Trading Co.', email: 'abc@trading.com', current: 0, d1_30: 0, d31_60: 0, d61_90: 16000, d90: 9500, risk: 'High' },
                    { customer: 'Cruz & Sons', email: 'cruz@sons.com', current: 12000, d1_30: 0, d31_60: 0, d61_90: 0, d90: 0, risk: 'Low' },
                    { customer: 'Reyes Corp', email: 'reyes@corp.com', current: 78000, d1_30: 0, d31_60: 0, d61_90: 0, d90: 0, risk: 'Low' },
                    { customer: 'Lim Trading', email: 'lim@trading.com', current: 0, d1_30: 19500, d31_60: 0, d61_90: 0, d90: 0, risk: 'Medium' },
                    { customer: 'Santos Ent.', email: 'santos@ent.com', current: 8000, d1_30: 28500, d31_60: 0, d61_90: 0, d90: 0, risk: 'Medium' },
                ],
                get filteredAgingData() {
                    if (this.overdueFilter === 'overdue') {
                        return this.agingData.filter(row => row.d1_30 > 0 || row.d31_60 > 0 || row.d61_90 > 0 || row.d90 > 0);
                    }
                    return this.agingData;
                },
                init() {
                    setTimeout(() => { this.barsLoaded = true; }, 150);
                },
                showReminderToast(customer) {
                    this.lastRemindedCustomer = customer;
                    this.showReminderToastFlag = true;
                    setTimeout(() => { this.showReminderToastFlag = false; }, 3500);
                }
            }
        }
    </script>
</body>
</html>
