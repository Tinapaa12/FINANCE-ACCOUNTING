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

                <h2 id="totalOutstanding">₱111,100</h2>

            </div>

        </div>

        <!-- Summary -->

        <div class="summary-card">

            <h2>Account Payable Summary</h2>

            <div class="summary-grid">

                <div class="summary orange-box">

                    <h4>Total Bills</h4>

                    <h1 id="totalBillsAmount">₱111,100</h1>

                    <p id="totalBillsCount">4 Bills</p>

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

                    <h1 id="totalPendingAmount">₱58,700</h1>

                    <p id="totalPendingCount">3 Bills</p>

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

            <tbody id="billsTableBody">

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
                        <button class="btn-edit" onclick="editBill(this)">✏</button>
                        <button class="btn-delete" onclick="deleteRow(this)">🗑</button>
                        <button class="btn-view" onclick="viewBill(this)">View</button>
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
                        <button class="btn-edit" onclick="editBill(this)">✏</button>
                        <button class="btn-delete" onclick="deleteRow(this)">🗑</button>
                        <button class="btn-view" onclick="viewBill(this)">View</button>
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
                        <button class="btn-edit" onclick="editBill(this)">✏</button>
                        <button class="btn-delete" onclick="deleteRow(this)">🗑</button>
                        <button class="btn-view" onclick="viewBill(this)">View</button>
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
                        <button class="btn-edit" onclick="editBill(this)">✏</button>
                        <button class="btn-delete" onclick="deleteRow(this)">🗑</button>
                        <button class="btn-view" onclick="viewBill(this)">View</button>
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
                        <button class="btn-edit" onclick="editBill(this)">✏</button>
                        <button class="btn-delete" onclick="deleteRow(this)">🗑</button>
                        <button class="btn-view" onclick="viewBill(this)">View</button>
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
                        <button class="btn-edit" onclick="editBill(this)">✏</button>
                        <button class="btn-delete" onclick="deleteRow(this)">🗑</button>
                        <button class="btn-view" onclick="viewBill(this)">View</button>
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

        <form id="addBillForm">

            <div class="modal-row">
                <label>Supplier :</label>
                <input type="text" id="billSupplier" placeholder="Supplier Name" required>
            </div>

            <div class="modal-row">
                <label>Amount :</label>
                <input type="number" id="billAmount" placeholder="₱0.00" step="0.01" required>
            </div>

            <div class="modal-row">
                <label>Due :</label>
                <input type="date" id="billDue" required>
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
<div id="editModal" class="modal">

    <div class="modal-box">

        <h2>Edit Supplier Bill</h2>

        <form id="editBillForm">

            <div class="modal-row">
                <label>Supplier :</label>
                <input type="text" id="editSupplier" placeholder="Supplier Name" required>
            </div>

            <div class="modal-row">
                <label>Amount :</label>
                <input type="number" id="editAmount" placeholder="₱0.00" step="0.01" required>
            </div>

            <div class="modal-row">
                <label>Due :</label>
                <input type="date" id="editDue" required>
            </div>

            <div class="modal-row">
                <label>Status :</label>
                <select id="editStatus">
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="modal-btn-cancel" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="modal-btn">
                    Save Changes
                </button>
            </div>

        </form>

    </div>

</div>

<style>
    /* Fallback modal show/hide styling in case it isn't already defined elsewhere */
    #billModal.modal,
    #viewModal.modal,
    #editModal.modal {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    #billModal.modal.active,
    #viewModal.modal.active,
    #editModal.modal.active {
        display: flex;
    }
    #viewModal .modal-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }
    #viewModal .modal-row label {
        font-weight: 600;
    }
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 16px;
    }
    .modal-btn-cancel {
        background-color: #e9ecef;
        color: #333;
        border: 1px solid #ccc;
        padding: 8px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.15s ease;
    }
    .modal-btn-cancel:hover {
        background-color: #dcdfe3;
    }
</style>

<script>
    let billCounter = document.querySelectorAll('#billsTableBody tr').length;

    function openModal() {
        document.getElementById('billModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('billModal').classList.remove('active');
        document.getElementById('addBillForm').reset();
    }

    // Close modal when clicking outside the modal box
    document.getElementById('billModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeModal();
        }
    });

    function formatCurrency(num) {
        return '₱' + num.toLocaleString('en-US', { minimumFractionDigits: 0 });
    }

    function formatDueDate(dateStr) {
        const date = new Date(dateStr + 'T00:00:00');
        return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric' });
    }

    document.getElementById('addBillForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const supplier = document.getElementById('billSupplier').value.trim();
        const amount = parseFloat(document.getElementById('billAmount').value);
        const due = document.getElementById('billDue').value;

        if (!supplier || isNaN(amount) || !due) {
            alert('Please fill in all fields.');
            return;
        }

        billCounter++;
        const billNo = 'BILL-' + String(billCounter).padStart(2, '0');

        // PO No. and Receipt/GRN No. aren't collected in the form, so they're
        // auto-generated to match the bill number. Update these manually via
        // the edit button if needed, or add fields to the modal later.
        const poNo = 'PO-2026-' + String(billCounter).padStart(3, '0');
        const grnNo = 'GRN-2026-' + String(billCounter).padStart(3, '0');

        // New bills default to "Pending" status until approved.
        const status = 'pending';
        const statusLabel = 'Pending';

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${billNo}</td>
            <td>${poNo}</td>
            <td>${grnNo}</td>
            <td>${supplier}</td>
            <td>${formatCurrency(amount)}</td>
            <td>${formatDueDate(due)}</td>
            <td><span class="status ${status}">${statusLabel}</span></td>
            <td class="actions">
                <button class="btn-edit" onclick="editBill(this)">✏</button>
                <button class="btn-delete" onclick="deleteRow(this)">🗑</button>
                <button class="btn-view" onclick="viewBill(this)">View</button>
            </td>
        `;

        document.getElementById('billsTableBody').appendChild(row);

        updateTotals();
        closeModal();
    });

    function deleteRow(btn) {
        if (confirm('Remove this bill?')) {
            btn.closest('tr').remove();
            updateTotals();
        }
    }

    // ---------- VIEW ----------

    document.getElementById('viewModal').addEventListener('click', function (e) {
        if (e.target === this) closeViewModal();
    });

    function closeViewModal() {
        document.getElementById('viewModal').classList.remove('active');
    }

    function viewBill(btn) {
        const row = btn.closest('tr');
        const cells = row.children;

        document.getElementById('viewBillNo').textContent = cells[0].textContent;
        document.getElementById('viewPoNo').textContent = cells[1].textContent;
        document.getElementById('viewGrnNo').textContent = cells[2].textContent;
        document.getElementById('viewSupplier').textContent = cells[3].textContent;
        document.getElementById('viewAmount').textContent = cells[4].textContent;
        document.getElementById('viewDue').textContent = cells[5].textContent;
        document.getElementById('viewStatus').textContent = cells[6].textContent.trim();

        document.getElementById('viewModal').classList.add('active');
    }

    // ---------- EDIT ----------

    let editingRow = null;

    document.getElementById('editModal').addEventListener('click', function (e) {
        if (e.target === this) closeEditModal();
    });

    function closeEditModal() {
        document.getElementById('editModal').classList.remove('active');
        document.getElementById('editBillForm').reset();
        editingRow = null;
    }

    function editBill(btn) {
        const row = btn.closest('tr');
        editingRow = row;
        const cells = row.children;

        document.getElementById('editSupplier').value = cells[3].textContent.trim();

        const amountText = cells[4].textContent.replace(/[₱,]/g, '');
        document.getElementById('editAmount').value = parseFloat(amountText);

        // The table only stores a formatted due date (e.g. "June 30"), not a
        // full ISO date, so the date input can't be pre-filled precisely.
        // Leave it blank for the user to re-pick if they want to change it.
        document.getElementById('editDue').value = '';

        const statusSpan = cells[6].querySelector('.status');
        const currentStatus = statusSpan.classList[1] || 'pending';
        document.getElementById('editStatus').value = currentStatus;

        document.getElementById('editModal').classList.add('active');
    }

    document.getElementById('editBillForm').addEventListener('submit', function (e) {
        e.preventDefault();

        if (!editingRow) return;

        const supplier = document.getElementById('editSupplier').value.trim();
        const amount = parseFloat(document.getElementById('editAmount').value);
        const due = document.getElementById('editDue').value;
        const status = document.getElementById('editStatus').value;
        const statusLabel = status.charAt(0).toUpperCase() + status.slice(1);

        if (!supplier || isNaN(amount)) {
            alert('Please fill in all fields.');
            return;
        }

        const cells = editingRow.children;
        cells[3].textContent = supplier;
        cells[4].textContent = formatCurrency(amount);

        // Only update the Due cell if a new date was actually picked.
        if (due) {
            cells[5].textContent = formatDueDate(due);
        }

        cells[6].innerHTML = `<span class="status ${status}">${statusLabel}</span>`;

        updateTotals();
        closeEditModal();
    });

    function updateTotals() {
        const rows = document.querySelectorAll('#billsTableBody tr');
        let total = 0;
        let pendingTotal = 0;
        let pendingCount = 0;

        rows.forEach(row => {
            const amountText = row.children[4].textContent.replace(/[₱,]/g, '');
            const amount = parseFloat(amountText) || 0;
            total += amount;

            const status = row.children[6].textContent.trim().toLowerCase();
            if (status === 'pending' || status === 'approved') {
                pendingTotal += amount;
                pendingCount++;
            }
        });

        document.getElementById('totalOutstanding').textContent = formatCurrency(total);
        document.getElementById('totalBillsAmount').textContent = formatCurrency(total);
        document.getElementById('totalBillsCount').textContent = rows.length + ' Bills';
        document.getElementById('totalPendingAmount').textContent = formatCurrency(pendingTotal);
        document.getElementById('totalPendingCount').textContent = pendingCount + ' Bills';
    }
</script>

@endsection