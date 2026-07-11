@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="top-section">
        <div class="summary-card">
            <h2>Dashboard</h2>
            <div class="summary-grid">
                <div class="summary orange-box">
                    <h4>Total Bills</h4>
                    <h1>₱{{ number_format($totalBillsAmount, 2) }}</h1>
                    <p>{{ $totalBillsCount }} Bills</p>
                </div>
                <div class="summary purple-box">
                    <h4>Pending Bills</h4>
                    <h1>{{ $pendingBillsCount }}</h1>
                    <p>Pending</p>
                </div>
                <div class="summary yellow-box">
                    <h4>Paid This Month</h4>
                    <h1>₱{{ number_format($paidThisMonthAmount, 2) }}</h1>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
