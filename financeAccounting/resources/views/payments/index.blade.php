@extends('layouts.app')

@section('content')

<div class="dashboard">

    <div class="payment-card">

        <div class="payment-header">
            <h2>Payments Made to Suppliers</h2>

            <div class="payment-actions">
                <input type="text" placeholder="Search Payment">
            </div>
        </div>

        <table class="payment-table">

            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Supplier</th>
                    <th>Amount</th>
                    <th>Payment Date</th>
                    <th>Method</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->bill_no }}</td>
                        <td>{{ $payment->supplier }}</td>
                        <td>₱{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') }}</td>
                        <td>Cash</td>
                        <td>
                            <span class="status {{ strtolower($payment->status) }}">
                                {{ $payment->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;">
                            No payments made yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</div>

@endsection