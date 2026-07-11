@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div>
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
