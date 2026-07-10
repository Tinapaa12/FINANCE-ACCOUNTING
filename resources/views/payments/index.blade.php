    @extends('layouts.app')

    @section('content')

    <div class="dashboard">

        <div class="payment-card">

            <div class="payment-header">

                <h2>Payments Made to supplires</h2>

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

                    <tr>
                        <td>PAY-001</td>
                        <td>PC Express</td>
                        <td>₱52,400</td>
                        <td>June 30, 2026</td>
                        <td>Bank Transfer</td>
                        <td><span class="payment-status">Paid</span></td>
                    </tr>

                    <tr>
                        <td>PAY-002</td>
                        <td>Complink</td>
                        <td>₱900</td>
                        <td>July 2, 2026</td>
                        <td>Cash</td>
                        <td><span class="payment-status">Paid</span></td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

    @endsection