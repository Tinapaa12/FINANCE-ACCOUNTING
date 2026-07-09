@extends('layouts.app')

@section('content')

<div class="dashboard">

    <div class="top-section">

        <!-- Upcoming Bills -->

        <div class="upcoming-card">

            <h2>Upcoming Supplier Bills</h2>

            <div class="bill-item">

                <div>
                    <h4>PC Express</h4>
                    <small>BILL-01 • Jun 5</small>
                </div>

                <div class="right">
                    <span class="amount orange-text">₱52,400</span>
                    <span class="due orange">Today</span>
                </div>

            </div>

            <div class="bill-item">

                <div>
                    <h4>Easy Express</h4>
                    <small>BILL-03 • Aug 24</small>
                </div>

                <div class="right">
                    <span class="amount blue-text">₱45,500</span>
                    <span class="due purple">3 Months 16 Days</span>
                </div>

            </div>

            <div class="bill-item">

                <div>
                    <h4>Complink</h4>
                    <small>BILL-05 • Dec 27</small>
                </div>

                <div class="right">
                    <span class="amount green-text">₱900</span>
                    <span class="due purple">5 Months 28 Days</span>
                </div>

            </div>

            <div class="bill-item">

                <div>
                    <h4>JDM Techno Computer Center</h4>
                    <small>BILL-06 • Feb 28</small>
                </div>

                <div class="right">
                    <span class="amount blue-text">₱12,300</span>
                    <span class="due purple">7 Months 29 Days</span>
                </div>

            </div>

            <div class="total">

                <h3>Total Outstanding :</h3>

                <h2>₱111,100</h2>

            </div>

        </div>

        <!-- Summary -->

        <div class="summary-card">

            <h2>Account Payable Summary</h2>

            <div class="summary-grid">

                <div class="summary orange-box">

                    <h4>Total Bills</h4>

                    <h1>₱111,100</h1>

                    <p>4 Bills</p>

                </div>

                <div class="summary yellow-box">

                    <h4>Paid This Month</h4>

                    <h1>₱54,400</h1>

                    <p>1 Payment</p>

                </div>

                <div class="summary green-box">

                    <h4>Payments Today</h4>

                    <h1>₱54,400</h1>

                    <p>1 Payment</p>

                </div>

                <div class="summary purple-box">

                    <h4>Total Bill Pending</h4>

                    <h1>₱58,700</h1>

                    <p>3 Bills</p>

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

                <tr>
                    <td>BILL-01</td>
                    <td>PO-2026-001</td>
                    <td>GRN-2026-001</td>
                    <td>PC Express</td>
                    <td>₱52,400</td>
                    <td>June 30</td>

                    <td>
                        <span class="status approved">Approved</span>
                    </td>

                    <td class="actions">
                        <button class="btn-edit">✏</button>
                        <button class="btn-delete">🗑</button>
                        <button class="btn-view">View</button>
                    </td>
                </tr>

                <tr>
                    <td>BILL-02</td>
                    <td>PO-2026-002</td>
                    <td>GRN-2026-002</td>
                    <td>Gilmore</td>
                    <td>₱2,400</td>
                    <td>Aug 24</td>

                    <td>
                        <span class="status paid">Paid</span>
                    </td>

                    <td class="actions">
                        <button class="btn-edit">✏</button>
                        <button class="btn-delete">🗑</button>
                        <button class="btn-view">View</button>
                    </td>
                </tr>

                <tr>
                    <td>BILL-03</td>
                    <td>PO-2026-003</td>
                    <td>GRN-2026-003</td>
                    <td>Easy Express</td>
                    <td>₱45,000</td>
                    <td>Oct 15</td>

                    <td>
                        <span class="status pending">Pending</span>
                    </td>

                    <td class="actions">
                        <button class="btn-edit">✏</button>
                        <button class="btn-delete">🗑</button>
                        <button class="btn-view">View</button>
                    </td>
                </tr>

                <tr>
                    <td>BILL-04</td>
                    <td>PO-2026-004</td>
                    <td>GRN-2026-004</td>
                    <td>Complink</td>
                    <td>₱900</td>
                    <td>Dec 27</td>

                    <td>
                        <span class="status approved">Approved</span>
                    </td>

                    <td class="actions">
                        <button class="btn-edit">✏</button>
                        <button class="btn-delete">🗑</button>
                        <button class="btn-view">View</button>
                    </td>
                </tr>

                <tr>
                    <td>BILL-05</td>
                    <td>PO-2026-005</td>
                    <td>GRN-2026-005</td>
                    <td>Dynaquest PC</td>
                    <td>₱35,990</td>
                    <td>Jan 17</td>

                    <td>
                        <span class="status paid">Paid</span>
                    </td>

                    <td class="actions">
                        <button class="btn-edit">✏</button>
                        <button class="btn-delete">🗑</button>
                        <button class="btn-view">View</button>
                    </td>
                </tr>

                <tr>
                    <td>BILL-06</td>
                    <td>PO-2026-006</td>
                    <td>GRN-2026-006</td>
                    <td>JDM Techno Computer Center</td>
                    <td>₱12,300</td>
                    <td>Feb 28</td>

                    <td>
                        <span class="status approved">Approved</span>
                    </td>

                    <td class="actions">
                        <button class="btn-edit">✏</button>
                        <button class="btn-delete">🗑</button>
                        <button class="btn-view">View</button>
                    </td>
                </tr>

            </tbody>

        </table>

    </div>

</div>
<!-- Modal -->
<div id="billModal" class="modal">

    <div class="modal-box">

        <h2>Add Supplier Bill</h2>

        <form>

            <div class="modal-row">
                <label>Supplier :</label>
                <input type="text" placeholder="Supplier Name">
            </div>

            <div class="modal-row">
                <label>Amount :</label>
                <input type="number" placeholder="₱0.00">
            </div>

            <div class="modal-row">
                <label>Due :</label>
                <input type="date">
            </div>

            <div class="modal-footer">
                <button type="submit" class="modal-btn">
                    Add Bill
                </button>
            </div>

        </form>

    </div>

</div>
@endsection