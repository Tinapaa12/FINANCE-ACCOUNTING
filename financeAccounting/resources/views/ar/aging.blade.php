@extends('layouts.app')

@section('title', 'Aging Report')
@section('page-heading', 'Aging Report')

@section('content')
<div x-data="reportApp()" x-cloak>

<div class="flex-1 overflow-y-auto content-scroll p-10 space-y-6 relative bg-dot-grid">
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-200/30 rounded-full blur-3xl pointer-events-none -z-0"></div>
            <div class="absolute top-96 left-0 w-72 h-72 bg-rose-200/20 rounded-full blur-3xl pointer-events-none -z-0"></div>

            <!-- Top navigation & Filters -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative">
                <div class="flex items-center gap-6">
                    <a href="{{ route('ar.overview') }}" class="text-[14px] text-gray-500 hover:text-[#2563eb] font-medium flex items-center gap-2"><i class="fas fa-arrow-left"></i> Back to A/R Overview</a>
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
                @foreach($summaryCards as $card)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5" style="background: {{ $card->color }}"></div>
                    <div class="flex items-center justify-between">
                        <p class="text-[14px] font-semibold" style="color: {{ $card->color }}">{{ $card->label }}</p>
                        <div class="w-8 h-8 rounded-full" style="background: {{ $card->color }}20; display: flex; align-items: center; justify-content: center">
                            <i class="fas fa-check" style="color: {{ $card->color }}; font-size: 12px"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 mt-1">P{{ number_format($card->total) }}</p>
                    <p class="text-[13px] text-gray-500">{{ $card->count }} Invoices</p>
                    <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all ease-out" style="transition-duration: 1100ms;" :style="'width: ' + (barsLoaded ? {{ $card->pct }} : 0) + '%'"></div>
                    </div>
                </div>
                @endforeach
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
                                <th class="px-5 py-3 text-center text-[#dc2626]">31-60 Days</th>
                                <th class="px-5 py-3 text-center text-[#b91c1c]">61+ Days</th>
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
                                    <td class="px-5 py-4 text-center" :class="row.current ? 'text-[#16a34a] font-medium' : 'text-gray-300'" x-text="row.current ? 'P'+Number(row.current).toLocaleString() : '-'"></td>
                                    <td class="px-5 py-4 text-center" :class="row.d1_30 ? 'text-[#ef4444] font-medium' : 'text-gray-300'" x-text="row.d1_30 ? 'P'+Number(row.d1_30).toLocaleString() : '-'"></td>
                                    <td class="px-5 py-4 text-center" :class="row.d31_60 ? 'text-[#dc2626] font-medium' : 'text-gray-300'" x-text="row.d31_60 ? 'P'+Number(row.d31_60).toLocaleString() : '-'"></td>
                                    <td class="px-5 py-4 text-center" :class="row.d61_90 ? 'text-[#b91c1c] font-medium' : 'text-gray-300'" x-text="row.d61_90 ? 'P'+Number(row.d61_90).toLocaleString() : '-'"></td>
                                    <td class="px-5 py-4 text-center font-bold text-gray-900" x-text="'P' + (Number(row.current)+Number(row.d1_30)+Number(row.d31_60)+Number(row.d61_90)).toLocaleString()"></td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-[12px] font-medium ring-1 ring-inset" 
                                              :class="{'bg-[#dcfce7] text-[#16a34a] ring-green-200': row.risk === 'Low', 
                                                       'bg-[#fef9c3] text-[#a16207] ring-yellow-200': row.risk === 'Medium', 
                                                       'bg-[#fee2e2] text-[#dc2626] ring-red-200': row.risk === 'High',
                                                       'bg-[#fce7f3] text-[#be185d] ring-pink-200': row.risk === 'Critical'}">
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
                                @foreach(['current', 'd1_30', 'd31_60', 'd61_90'] as $bucket)
                                <td class="px-5 py-4 text-center" style="color: {{ match($bucket) {'current' => '#16a34a', 'd1_30' => '#ef4444', 'd31_60' => '#dc2626', 'd61_90' => '#b91c1c'} }}">
                                    P{{ number_format($totals[$bucket]) }}
                                </td>
                                @endforeach
                                <td class="px-5 py-4 text-center text-[#4338ca]">P{{ number_format($totalOverall) }}</td>
                                <td></td><td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

<!-- TOAST: Reminder Sent -->
    <div x-show="showReminderToastFlag" x-transition.duration.300ms class="fixed top-10 right-10 bg-white rounded-xl shadow-2xl border-l-[6px] border-[#2563eb] p-5 w-[280px] flex items-center gap-4 z-[60]" x-cloak>
        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl shadow-md"><i class="fas fa-check"></i></div>
        <div>
            <h4 class="font-bold text-gray-900 text-[15px]">Reminder Sent!</h4>
            <p class="text-[13px] text-gray-500" x-text="'to ' + lastRemindedCustomer"></p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function reportApp() {
            return {
                showReminderToastFlag: false,
                lastRemindedCustomer: '',
                overdueFilter: 'all',
                barsLoaded: false,
                agingData: @json($customerRows),
                get filteredAgingData() {
                    if (this.overdueFilter === 'overdue') {
                        return this.agingData.filter(row => row.d1_30 > 0 || row.d31_60 > 0 || row.d61_90 > 0);
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
@endpush
