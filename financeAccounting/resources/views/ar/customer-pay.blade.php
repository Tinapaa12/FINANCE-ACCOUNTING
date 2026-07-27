@extends('layouts.app')

@section('title', 'Customer Dummy')
@section('page-heading', 'Customer Dummy')

@section('content')
<div x-data="customerPayApp()" x-cloak>
    <div class="flex-1 overflow-y-auto content-scroll p-10 space-y-6 relative bg-dot-grid">

        <div class="flex items-center gap-4">
            <a href="{{ route('ar.overview') }}" class="text-[14px] text-gray-500 hover:text-[#2563eb] font-medium flex items-center gap-2"><i class="fas fa-arrow-left"></i> Back to A/R Overview</a>
            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-envelope-open-text text-indigo-500"></i> Customer Dummy — Dunning Letters</h3>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center justify-between">
            <p class="text-[14px] text-gray-500">Dunning letters sent from the Aging Report will appear here.</p>
            <button @click="fetchLetters()" class="text-[13px] text-indigo-600 hover:text-indigo-700 font-medium flex items-center gap-1.5">
                <i class="fas fa-sync" :class="loading ? 'fa-spin' : ''"></i> Refresh
            </button>
        </div>

        <template x-if="loading">
            <div class="text-center py-16 text-gray-400"><i class="fas fa-spinner fa-spin text-2xl"></i></div>
        </template>

        <template x-if="!loading && letters.length === 0">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-16 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4"><i class="fas fa-inbox text-gray-300 text-2xl"></i></div>
                <p class="text-gray-400 text-[15px]">No dunning letters yet.</p>
                <p class="text-gray-300 text-[13px] mt-1">Click "Remind" on the Aging Report to send one.</p>
            </div>
        </template>

        <template x-if="!loading && letters.length > 0">
            <div class="space-y-4">
                <template x-for="letter in letters" :key="letter.customer + '|' + letter.ref">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">
                        <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-gray-800 text-[15px]" x-text="letter.customer"></h4>
                                <p class="text-[12px] text-gray-400" x-text="'Ref: ' + (letter.ref || 'N/A')"></p>
                            </div>
                            <span class="text-[12px] font-medium px-3 py-1 rounded-full"
                                  :class="letter.days_overdue > 10 ? 'bg-red-50 text-red-600' : 'bg-yellow-50 text-yellow-600'"
                                  x-text="letter.days_overdue + ' day(s) overdue'"></span>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <p class="text-[13px] text-gray-700 leading-relaxed" x-text="letter.message_sent"></p>
                            </div>
                            <div class="flex items-center justify-between bg-yellow-50 border border-yellow-200 rounded-lg px-5 py-4">
                                <div>
                                    <p class="text-[13px] text-yellow-700 font-medium">Remaining Balance</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-0.5" x-text="'₱' + Number(letter.amount).toLocaleString()"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[13px] text-gray-400">Due Date</p>
                                    <p class="font-semibold text-gray-700" x-text="letter.due_date || 'N/A'"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-[13px]">
                                <div>
                                    <span class="text-gray-400">Phone:</span>
                                    <p class="font-medium text-gray-700" x-text="letter.phone || 'N/A'"></p>
                                </div>
                                <div>
                                    <span class="text-gray-400">Type:</span>
                                    <p class="font-medium text-gray-700" x-text="letter.type"></p>
                                </div>
                                <div>
                                    <span class="text-gray-400">Order:</span>
                                    <p class="font-medium text-gray-700" x-text="letter.ref || 'N/A'"></p>
                                </div>
                            </div>
                            <div class="pt-2">
                                <template x-if="letter.txn_id">
                                    <button @click="payItem(letter)" :disabled="paying === letter.ref"
                                            class="w-full bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-[16px] font-bold px-6 py-3.5 rounded-xl transition shadow-md flex items-center justify-center gap-3">
                                        <i class="fas text-[18px]" :class="paying === letter.ref ? 'fa-spinner fa-spin' : 'fa-credit-card'"></i>
                                        <span x-text="paying === letter.ref ? 'PROCESSING...' : 'PAY NOW — ₱' + Number(letter.amount).toLocaleString()"></span>
                                    </button>
                                </template>
                                <template x-if="!letter.txn_id">
                                    <div class="w-full text-center bg-gray-50 border border-gray-200 rounded-xl px-6 py-3.5">
                                        <p class="text-[14px] text-gray-400 font-medium"><i class="fas fa-check-circle text-emerald-400 mr-2"></i> Already Paid / Processed</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="paidMessage">
            <div class="fixed top-10 right-10 bg-white rounded-xl shadow-2xl border-l-[6px] border-emerald-500 p-5 w-[400px] z-[60] transition-all duration-300">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-green-600 flex items-center justify-center text-white text-base shadow-md shrink-0 mt-0.5"><i class="fas fa-check"></i></div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-gray-900 text-[14px]">Payment Successful</h4>
                        <p class="text-[12px] text-gray-500 mt-1" x-text="paidMessage"></p>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function customerPayApp() {
        return {
            loading: false,
            letters: [],
            paying: null,
            paidMessage: '',
            init() {
                this.fetchLetters();
            },
            fetchLetters() {
                this.loading = true;
                var self = this;
                fetch('/api/ar/aging-report/remind')
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        self.loading = false;
                        if (!data.success) return;
                        self.letters = data.reminded_customers || [];
                    })
                    .catch(function(e) {
                        self.loading = false;
                    });
            },
            payItem(item) {
                if (this.paying || !item.txn_id) return;
                this.paying = item.ref;
                this.paidMessage = '';
                var self = this;
                fetch('/api/sales-transactions/' + item.txn_id + '/mark-as-paid', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    self.paying = null;
                    if (!data.success) {
                        alert('Payment failed: ' + (data.message || 'Unknown error'));
                        return;
                    }
                    self.paidMessage = item.ref + ' - Paid successfully.';
                    if (data.payment_data) {
                        self.paidMessage += ' ' + (data.payment_data.message || '');
                    }
                    self.letters = self.letters.filter(function(i) { return i.ref !== item.ref; });
                    fetch('/api/ar/aging-report/remind?remove=' + encodeURIComponent(item.ref));
                    setTimeout(function() { self.paidMessage = ''; }, 5000);
                })
                .catch(function(e) {
                    self.paying = null;
                    alert('Payment failed: ' + e.message);
                });
            }
        }
    }
</script>
@endpush