@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Workflow Flow -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <h2 class="text-lg font-semibold mb-6">Account Payable Workflow</h2>
        <div class="flex items-center justify-between">
            <a href="{{ route('purchase-orders.index') }}" class="flex flex-col items-center group">
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="mt-2 text-sm font-semibold text-gray-700 group-hover:text-blue-600 transition-colors">Purchase Order</span>
                <span class="text-xs text-gray-400">PO</span>
            </a>

            <div class="flex-1 mx-4 flex items-center justify-center">
                <div class="w-full h-0.5 bg-gray-300 relative">
                    <svg class="absolute -right-2 -top-2 w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>

            <a href="{{ route('goods-received-notes.index') }}" class="flex flex-col items-center group">
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition-colors">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
                <span class="mt-2 text-sm font-semibold text-gray-700 group-hover:text-green-600 transition-colors">Goods Received</span>
                <span class="text-xs text-gray-400">GRN</span>
            </a>

            <div class="flex-1 mx-4 flex items-center justify-center">
                <div class="w-full h-0.5 bg-gray-300 relative">
                    <svg class="absolute -right-2 -top-2 w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>

            <a href="{{ route('supplier-bills.index') }}" class="flex flex-col items-center group">
                <div class="w-16 h-16 rounded-full bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="mt-2 text-sm font-semibold text-gray-700 group-hover:text-purple-600 transition-colors">Supplier Bill</span>
                <span class="text-xs text-gray-400">Invoice</span>
            </a>

            <div class="flex-1 mx-4 flex items-center justify-center">
                <div class="w-full h-0.5 bg-gray-300 relative">
                    <svg class="absolute -right-2 -top-2 w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>

            <a href="{{ route('payments.index') }}" class="flex flex-col items-center group">
                <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="mt-2 text-sm font-semibold text-gray-700 group-hover:text-orange-600 transition-colors">Payment Made</span>
                <span class="text-xs text-gray-400">Payment</span>
            </a>
        </div>
    </div>

    <!-- Dashboard Stats -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <h2 class="text-lg font-semibold mb-4">Dashboard Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-5 rounded-xl bg-orange-50 border border-orange-200">
                <p class="text-sm font-medium text-orange-800">Total Bills</p>
                <p class="text-2xl font-bold text-orange-900 mt-1">₱{{ number_format($totalBillsAmount, 2) }}</p>
                <p class="text-sm text-orange-600">{{ $totalBillsCount }} Bills</p>
            </div>
            <div class="p-5 rounded-xl bg-purple-50 border border-purple-200">
                <p class="text-sm font-medium text-purple-800">Pending</p>
                <p class="text-2xl font-bold text-purple-900 mt-1">{{ $pendingBillsCount }}</p>
                <p class="text-sm text-purple-600">Awaiting approval</p>
            </div>
            <div class="p-5 rounded-xl bg-green-50 border border-green-200">
                <p class="text-sm font-medium text-green-800">Paid This Month</p>
                <p class="text-2xl font-bold text-green-900 mt-1">₱{{ number_format($paidThisMonthAmount, 2) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
