@extends('layouts.app')

@section('content')

<div class="dashboard">

    <div class="top-section">

        <!-- Upcoming Bills -->

        <div class="upcoming-card">

    <h2>Upcoming Supplier Bills</h2>

    @foreach($upcomingBills as $bill)

        <div class="bill-item">

            <div>
                <h4>{{ $bill->supplier }}</h4>
                <small>{{ $bill->bill_no }} • {{ \Carbon\Carbon::parse($bill->due_date)->format('M d') }}</small>
            </div>

            <div class="right">
                <span class="amount blue-text">
                    ₱{{ number_format($bill->amount, 2) }}
                </span>

                <span class="due purple">
                    {{ \Carbon\Carbon::parse($bill->due_date)->diffForHumans() }}
                </span>
            </div>

        </div>

    @endforeach

    <div class="total">
        <h3>Total Outstanding :</h3>

        <h2>
            ₱{{ number_format($totalBillsAmount,2) }}
        </h2>
    </div>

</div>

        <!-- Summary -->

        <div class="summary-card">

            <h2>Account Payable Summary</h2>

            <div class="summary-grid">

                <div class="summary orange-box">

                    <h4>Total Bills</h4>

<h1>₱{{ number_format($totalBillsAmount, 2) }}</h1>
<p>{{ $totalBillsCount }} Bills</p>

                </div>

                <div class="summary yellow-box">

                    <h4>Paid This Month</h4>

<h1>₱{{ number_format($paidThisMonthAmount, 2) }}</h1>
<p>{{ $paidThisMonthCount }} Payments</p>

                </div>

                <div class="summary green-box">

                    <h4>Payments Today</h4>

<h1>₱{{ number_format($paymentsTodayAmount, 2) }}</h1>
<p>{{ $paymentsTodayCount }} Payments</p>

                </div>

                <div class="summary purple-box">

                    <h4>Total Bill Pending</h4>

<h1>₱{{ number_format($pendingBillsAmount, 2) }}</h1>
<p>{{ $pendingBillsCount }} Bills</p>

                </div>

            </div>

        </div>

    </div>
    <!-- Supplier Bills Table -->

<div class="table-card">

    <div class="table-header">

        <h2>Supplier Bills</h2>

        <button class="add-btn" onclick="openModal()">Add Bill</button>

    </div>

    <div class="table-responsive">

        <table>

            <thead>

                <tr>
                    <th>Bill No.</th>
                    <th>PO No.</th>
                    <th>Receipt/GRN No.</th>
                    <th>Supplier</th>
                    <th>Amount</th>
                    <th>Due</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>

            </thead>

            <tbody>
@forelse($supplierBills as $bill)
<tr>
    <td>{{ $bill->bill_no }}</td>
    <td>{{ $bill->po_no }}</td>
    <td>{{ $bill->grn_no }}</td>
    <td>{{ $bill->supplier }}</td>
    <td>₱{{ number_format($bill->amount, 2) }}</td>
    <td>{{ $bill->due_date }}</td>
    <td>
        <span class="status {{ strtolower($bill->status) }}">
            {{ $bill->status }}
        </span>
    </td>
    <td class="actions">
<button
    class="btn-edit"
    onclick="editBill(
        {{ $bill->id }},
        '{{ $bill->supplier }}',
        {{ $bill->amount }},
        '{{ $bill->due_date }}',
        '{{ $bill->status }}'
    )">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
</button>

<form action="{{ route('supplier-bills.destroy', $bill->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')

    <button type="submit" class="btn-delete"
        onclick="return confirm('Delete this bill?')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
    </button>
</form>

<button class="btn-view" onclick="viewBill(this)">View</button>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" style="text-align:center;">
        No supplier bills found.
    </td>
</tr>
@endforelse
</tbody>

        </table>

    </div>

</div>
<!-- Modal -->
<div id="billModal" class="modal">
    <div class="modal-box">

        <h2>Add Supplier Bill</h2>

        <form method="POST" action="{{ route('supplier-bills.store') }}">
            @csrf

            <div class="modal-row">
                <label>Supplier :</label>
                <input type="text" name="supplier" required>
            </div>

            <div class="modal-row">
                <label>Amount :</label>
                <input type="number" name="amount" step="0.01" required>
            </div>

            <div class="modal-row">
                <label>Due Date :</label>
                <input type="date" name="due_date" required>
            </div>

            <div class="modal-row">
                <label>Status :</label>
                <select name="status">
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="modal-btn-cancel" onclick="closeModal()">Cancel</button>

                <button type="submit" class="modal-btn">
                    Add Bill
                </button>
            </div>

        </form>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="modal">

    <div class="modal-box">

        <h2>Bill Details</h2>

        <div class="modal-row"><label>Bill No. :</label> <span id="viewBillNo"></span></div>
        <div class="modal-row"><label>PO No. :</label> <span id="viewPoNo"></span></div>
        <div class="modal-row"><label>Receipt/GRN No. :</label> <span id="viewGrnNo"></span></div>
        <div class="modal-row"><label>Supplier :</label> <span id="viewSupplier"></span></div>
        <div class="modal-row"><label>Amount :</label> <span id="viewAmount"></span></div>
        <div class="modal-row"><label>Due :</label> <span id="viewDue"></span></div>
        <div class="modal-row"><label>Status :</label> <span id="viewStatus"></span></div>

        <div class="modal-footer">
            <button type="button" class="modal-btn-cancel" onclick="closeViewModal()">Close</button>
        </div>

    </div>

</div>

<!-- Edit Modal -->
<!-- Edit Modal -->
<div id="editModal" class="modal">

    <div class="modal-box">

        <h2>Edit Supplier Bill</h2>

        <form id="editBillForm" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-row">
                <label>Supplier :</label>
                <input type="text" name="supplier" id="editSupplier" required>
            </div>

            <div class="modal-row">
                <label>Amount :</label>
                <input type="number" name="amount" id="editAmount" step="0.01" required>
            </div>

            <div class="modal-row">
                <label>Due :</label>
                <input type="date" name="due_date" id="editDue" required>
            </div>

            <div class="modal-row">
                <label>Status :</label>

                <select name="status" id="editStatus">
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="modal-btn-cancel"
                    onclick="closeEditModal()">
                    Cancel
                </button>

                <button
                    type="submit"
                    class="modal-btn">
                    Save Changes
                </button>
            </div>

        </form>

    </div>

</div>

@endsection