// ---------- ADD BILL MODAL ----------

function openModal() {
    document.getElementById("billModal").classList.add("active");
}

function closeModal() {
    document.getElementById("billModal").classList.remove("active");
}

document.getElementById("billModal").addEventListener("click", function (e) {
    if (e.target === this) {
        closeModal();
    }
});

// ---------- VIEW MODAL ----------

function viewBill(btn) {
    const row = btn.closest("tr");
    const cells = row.querySelectorAll("td");

    document.getElementById("viewBillNo").textContent = cells[0].textContent;
    document.getElementById("viewPoNo").textContent = cells[1].textContent;
    document.getElementById("viewGrnNo").textContent = cells[2].textContent;
    document.getElementById("viewSupplier").textContent = cells[3].textContent;
    document.getElementById("viewAmount").textContent = cells[4].textContent;
    document.getElementById("viewDue").textContent = cells[5].textContent;
    document.getElementById("viewStatus").textContent = cells[6].textContent;
<<<<<<< HEAD
=======
    document.getElementById("viewPaymentMethod").textContent = row.getAttribute('data-payment-method') || '';
>>>>>>> origin/paymentsMade

    document.getElementById("viewModal").classList.add("active");
}

function closeViewModal() {
    document.getElementById("viewModal").classList.remove("active");
}

document.getElementById("viewModal").addEventListener("click", function (e) {
    if (e.target === this) {
        closeViewModal();
    }
});

// ---------- EDIT MODAL ----------

let editingRow = null;

<<<<<<< HEAD
function editBill(id, supplier, amount, dueDate, status)
=======
function editBill(id, supplier, amount, dueDate, status, paymentMethod, ewtRate, terms)
>>>>>>> origin/paymentsMade
{
    document.getElementById('editSupplier').value = supplier;
    document.getElementById('editAmount').value = amount;
    document.getElementById('editDue').value = dueDate;
    document.getElementById('editStatus').value = status;
<<<<<<< HEAD
=======
    document.getElementById('editPaymentMethod').value = paymentMethod;
    document.getElementById('editEwtRate').value = ewtRate || '';
    document.getElementById('editPaymentTerms').value = terms || '';
>>>>>>> origin/paymentsMade

    document.getElementById('editBillForm').action =
        "/supplier-bills/" + id;

    document.getElementById('editModal').classList.add('active');
}

document.getElementById("editModal").addEventListener("click", function (e) {
    if (e.target === this) {
        closeEditModal();
    }
});

function closeEditModal() {
    document.getElementById("editModal").classList.remove("active");
}
<<<<<<< HEAD
=======
// ---------- PAID (AJAX) ----------

function markAsPaid(id, btn) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value;

    fetch('/supplier-bills/' + id + '/pay', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;

        const row = btn.closest('tr');
        const now = new Date();
        const formattedDate = now.toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) + ' ' + now.toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true});

        // Update row data attributes
        row.setAttribute('data-status', 'Paid');
        row.setAttribute('data-paid-at', formattedDate);

        // Update status badge
        const statusCell = row.querySelectorAll('td')[6];
        statusCell.innerHTML = '<span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Paid</span>';

        // Remove the Paid button that was clicked
        if (btn && btn.parentNode) btn.remove();

        // Update summary cards
        document.getElementById('paidMonthAmount').textContent = '₱' + Number(data.paidThisMonthAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('paidMonthCount').textContent = data.paidThisMonthCount + ' Payments';
        document.getElementById('paidTodayAmount').textContent = '₱' + Number(data.paymentsTodayAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('paidTodayCount').textContent = data.paymentsTodayCount + ' Payments';
        document.getElementById('pendingAmount').textContent = '₱' + Number(data.pendingBillsAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('pendingCount').textContent = data.pendingBillsCount + ' Bills';
        document.getElementById('totalOutstanding').textContent = '₱' + Number(data.totalOutstanding).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

        const el = (id) => document.getElementById(id);
        if (el('overdueAmount')) {
            el('overdueAmount').textContent = '₱' + Number(data.overdueAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
        if (el('overdueCount')) {
            el('overdueCount').textContent = data.overdueCount + ' Overdue';
        }

        // Refresh upcoming bills
        const upcomingContainer = document.getElementById('upcomingBills');
        if (upcomingContainer) {
            upcomingContainer.innerHTML = '';
            data.upcomingBills.forEach(b => {
                const div = document.createElement('div');
                div.className = 'flex justify-between items-center py-3 border-b border-gray-200';
                div.innerHTML = '<div><h4 class="font-medium">' + b.supplier + '</h4><p class="text-sm text-gray-500">' + b.bill_no + ' \u2022 ' + new Date(b.due_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric'}) + '</p></div><div class="text-right"><span class="block font-semibold text-blue-600">\u20B1' + Number(b.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span><span class="text-xs px-2 py-1 rounded-full text-white bg-purple-400">' + b.diff + '</span></div>';
                upcomingContainer.appendChild(div);
            });
        }

        // Refresh overdue bills
        const overdueContainer = document.getElementById('overdueBills');
        if (overdueContainer && data.overdueBills) {
            overdueContainer.innerHTML = '';
            if (data.overdueBills.length > 0) {
                data.overdueBills.forEach(b => {
                    const div = document.createElement('div');
                    div.className = 'flex justify-between items-center py-2 border-b border-red-100';
                    div.innerHTML = '<div><h4 class="font-medium text-sm">' + b.supplier + '</h4><p class="text-xs text-gray-500">' + b.bill_no + '</p></div><div class="text-right"><span class="block font-semibold text-red-600 text-sm">\u20B1' + Number(b.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span><span class="text-xs px-2 py-0.5 rounded-full text-white bg-red-500">' + b.overdueDays + ' days overdue</span></div>';
                    overdueContainer.appendChild(div);
                });
            } else {
                const overdueSection = overdueContainer.closest('.mt-6');
                if (overdueSection) overdueSection.remove();
            }
        }
    })
    .catch(() => {});
}

// ---------- RECEIPT ----------

function showReceipt(id, btn) {
    const row = btn.closest('tr');

    document.getElementById('receiptNo').textContent = 'RCP-' + String(id).padStart(2, '0');
    document.getElementById('receiptBillNo').textContent = row.getAttribute('data-bill-no');
    document.getElementById('receiptPoNo').textContent = row.getAttribute('data-po-no');
    document.getElementById('receiptGrnNo').textContent = row.getAttribute('data-grn-no');
    document.getElementById('receiptSupplier').textContent = row.getAttribute('data-supplier');
    document.getElementById('receiptAmount').textContent = '₱' + Number(row.getAttribute('data-amount')).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('receiptPaymentMethod').textContent = row.getAttribute('data-payment-method') || 'N/A';
    document.getElementById('receiptDatePaid').textContent = row.getAttribute('data-paid-at') || 'N/A';
    document.getElementById('receiptStatus').textContent = row.getAttribute('data-status');

    document.getElementById('receiptModal').classList.add('active');
}

function closeReceiptModal() {
    document.getElementById('receiptModal').classList.remove('active');
}

document.getElementById('receiptModal').addEventListener('click', function (e) {
    if (e.target === this) {
        closeReceiptModal();
    }
});

function printReceipt() {
    window.print();
}

>>>>>>> origin/paymentsMade
// ---------- GLOBAL ----------

window.openModal = openModal;
window.closeModal = closeModal;
window.viewBill = viewBill;
window.closeViewModal = closeViewModal;
window.editBill = editBill;
window.closeEditModal = closeEditModal;
<<<<<<< HEAD
=======
window.markAsPaid = markAsPaid;
window.showReceipt = showReceipt;
window.closeReceiptModal = closeReceiptModal;
window.printReceipt = printReceipt;
>>>>>>> origin/paymentsMade
