// ===== MODAL HELPERS =====
function _closeModal(id) { var el = document.getElementById(id); if (el) el.classList.remove('active'); }
function _openModal(id) { var el = document.getElementById(id); if (el) el.classList.add('active'); }
function initBackdropClose(id) {
    var el = document.getElementById(id);
    if (el) el.addEventListener('click', function(e) { if (e.target === this) _closeModal(id); });
}

// No-arg close functions used in onclick attributes
function closeModal() { _closeModal('billModal'); }

function closeEditModal() { _closeModal('editModal'); }
function closeEditGRNModal() { _closeModal('editGRNModal'); }
function closeEditPOModal() { _closeModal('editPOModal'); }
function closeGRNModal() { _closeModal('grnModal'); }
function closePaymentModal() { _closeModal('paymentModal'); }
function closePOModal() { _closeModal('poModal'); }
function closeReceiptModal() { _closeModal('receiptModal'); }
function closeViewModal() { _closeModal('viewModal'); }

// ===== SUPPLIER BILLS =====
function openViewModal(id, supplier, amount, due, status, paymentMethod, billNo, poNo, grnNo, attachments, payments, ewtRate, terms, stockRequestNo) {
    document.getElementById('viewSupplier').textContent = supplier;
    document.getElementById('viewAmount').textContent = '\u20B1' + Number(amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('viewDue').textContent = due;
    document.getElementById('viewStatus').textContent = status;
    document.getElementById('viewPaymentMethod').textContent = paymentMethod || '-';
    document.getElementById('viewEwtRate').textContent = ewtRate ? ewtRate + '%' : '-';
    document.getElementById('viewTerms').textContent = terms || '-';
    document.getElementById('viewBillNo').textContent = billNo || '-';
    document.getElementById('viewPoNo').textContent = poNo || '-';
    document.getElementById('viewGrnNo').textContent = grnNo || '-';
    document.getElementById('viewStockRequestNo').textContent = stockRequestNo || '-';
    document.getElementById('uploadForm').action = '/supplier-bills/' + id + '/attachments';
    document.getElementById('uploadBillId').value = id;

    var container = document.getElementById('viewAttachments');
    container.innerHTML = '';
    if (attachments && attachments.length) {
        attachments.forEach(function(a) {
            var link = document.createElement('a');
            link.href = '/attachments/' + a.id + '/download';
            link.className = 'block text-xs text-blue-600 hover:underline';
            link.textContent = a.original_filename;
            container.appendChild(link);
        });
    } else {
        container.innerHTML = '<p class="text-xs text-gray-400">No attachments.</p>';
    }

    var pContainer = document.getElementById('viewPayments');
    pContainer.innerHTML = '';
    if (payments && payments.length) {
        payments.forEach(function(p) {
            var row = document.createElement('div');
            row.className = 'flex justify-between items-center py-1.5 text-xs border-b border-gray-50';
            row.innerHTML = '<span>' + (p.date || '-') + '</span><span>\u20B1' + Number(p.amount).toLocaleString(undefined, {minimumFractionDigits: 2}) + '</span><span class="text-gray-500">' + (p.method || '-') + '</span>';
            pContainer.appendChild(row);
        });
    } else {
        pContainer.innerHTML = '<p class="text-xs text-gray-400">No payments recorded.</p>';
    }

    _openModal('viewModal');
}

function openPaymentModal(id, amount, totalPaid, method) {
    var balance = amount - totalPaid;
    document.getElementById('paymentBillId').value = id;
    document.getElementById('paymentBillAmount').textContent = '\u20B1' + Number(amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('paymentRemaining').textContent = '\u20B1' + Number(balance).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('paymentAmount').value = '';
    document.getElementById('paymentAmount').max = balance;
    document.getElementById('paymentMethod').value = method || '';
    _openModal('paymentModal');
}

function markAsPaid(id, btn) {
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value;

    fetch('/supplier-bills/' + id + '/pay', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.success) return;

        var row = btn.closest('tr');
        var now = new Date();
        var formattedDate = now.toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) + ' ' + now.toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true});

        row.setAttribute('data-status', 'Paid');
        row.setAttribute('data-paid-at', formattedDate);

        var statusCell = row.querySelectorAll('td')[6];
        statusCell.innerHTML = '<span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Paid</span>';

        if (btn && btn.parentNode) btn.remove();

        document.getElementById('paidMonthAmount').textContent = '\u20B1' + Number(data.paidThisMonthAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('paidMonthCount').textContent = data.paidThisMonthCount + ' Payments';
        document.getElementById('paidTodayAmount').textContent = '\u20B1' + Number(data.paymentsTodayAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('paidTodayCount').textContent = data.paymentsTodayCount + ' Payments';
        document.getElementById('pendingAmount').textContent = '\u20B1' + Number(data.pendingBillsAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('pendingCount').textContent = data.pendingBillsCount + ' Bills';
        document.getElementById('totalOutstanding').textContent = '\u20B1' + Number(data.totalOutstanding).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

        var el = function(id) { return document.getElementById(id); };
        if (el('overdueAmount')) el('overdueAmount').textContent = '\u20B1' + Number(data.overdueAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        if (el('overdueCount')) el('overdueCount').textContent = data.overdueCount + ' Overdue';

        var upcomingContainer = document.getElementById('upcomingBills');
        if (upcomingContainer) {
            upcomingContainer.innerHTML = '';
            data.upcomingBills.forEach(function(b) {
                var div = document.createElement('div');
                div.className = 'flex justify-between items-center py-3 border-b border-gray-200';
                div.innerHTML = '<div><h4 class="font-medium">' + b.supplier + '</h4><p class="text-sm text-gray-500">' + b.bill_no + ' \u2022 ' + new Date(b.due_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric'}) + '</p></div><div class="text-right"><span class="block font-semibold text-blue-600">\u20B1' + Number(b.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span><span class="text-xs px-2 py-1 rounded-full text-white bg-purple-400">' + b.diff + '</span></div>';
                upcomingContainer.appendChild(div);
            });
        }

        var overdueContainer = document.getElementById('overdueBills');
        if (overdueContainer && data.overdueBills) {
            overdueContainer.innerHTML = '';
            if (data.overdueBills.length > 0) {
                data.overdueBills.forEach(function(b) {
                    var div = document.createElement('div');
                    div.className = 'flex justify-between items-center py-2 border-b border-red-100';
                    div.innerHTML = '<div><h4 class="font-medium text-sm">' + b.supplier + '</h4><p class="text-xs text-gray-500">' + b.bill_no + '</p></div><div class="text-right"><span class="block font-semibold text-red-600 text-sm">\u20B1' + Number(b.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span><span class="text-xs px-2 py-0.5 rounded-full text-white bg-red-500">' + b.overdueDays + ' days overdue</span></div>';
                    overdueContainer.appendChild(div);
                });
            } else {
                var overdueSection = overdueContainer.closest('.mt-6');
                if (overdueSection) overdueSection.remove();
            }
        }
    })
    .catch(function() {});
}

function showReceipt(id, btn) {
    var row = btn.closest('tr');
    document.getElementById('receiptNo').textContent = 'RCP-' + String(id).padStart(2, '0');
    document.getElementById('receiptBillNo').textContent = row.getAttribute('data-bill-no');
    document.getElementById('receiptPoNo').textContent = row.getAttribute('data-po-no');
    document.getElementById('receiptGrnNo').textContent = row.getAttribute('data-grn-no');
    document.getElementById('receiptSupplier').textContent = row.getAttribute('data-supplier');
    document.getElementById('receiptAmount').textContent = '\u20B1' + Number(row.getAttribute('data-amount')).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('receiptPaymentMethod').textContent = row.getAttribute('data-payment-method') || 'N/A';
    document.getElementById('receiptDatePaid').textContent = row.getAttribute('data-paid-at') || 'N/A';
    document.getElementById('receiptStatus').textContent = row.getAttribute('data-status');
    _openModal('receiptModal');
}

function viewBill(btn) {
    var row = btn.closest('tr');
    document.getElementById('viewBillNo').textContent = row.getAttribute('data-bill-no') || '-';
    document.getElementById('viewPoNo').textContent = row.getAttribute('data-po-no') || '-';
    document.getElementById('viewGrnNo').textContent = row.getAttribute('data-grn-no') || '-';
    document.getElementById('viewStockRequestNo').textContent = row.getAttribute('data-stock-request-no') || '-';
    document.getElementById('viewSupplier').textContent = row.getAttribute('data-supplier') || '';
    document.getElementById('viewAmount').textContent = '\u20B1' + Number(row.getAttribute('data-amount')).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('viewDue').textContent = row.getAttribute('data-due-date') || '';
    document.getElementById('viewStatus').textContent = row.getAttribute('data-status') || '';
    document.getElementById('viewPaymentMethod').textContent = row.getAttribute('data-payment-method') || '';
    document.getElementById('viewMatchingStatus').textContent = row.getAttribute('data-matching-status') || 'Unmatched';
    document.getElementById('viewMatchingNotes').textContent = row.getAttribute('data-matching-notes') || '-';
    _openModal('viewModal');
}

function editBill(id, supplier, amount, dueDate, status, paymentMethod, ewtRate, terms) {
    document.getElementById('editSupplier').value = supplier;
    document.getElementById('editAmount').value = amount;
    document.getElementById('editDue').value = dueDate;
    document.getElementById('editStatus').value = status;
    document.getElementById('editPaymentMethod').value = paymentMethod;
    document.getElementById('editEwtRate').value = ewtRate || '';
    document.getElementById('editPaymentTerms').value = terms || '';
    document.getElementById('editBillForm').action = '/supplier-bills/' + id;
    _openModal('editModal');
}

// ===== PURCHASE ORDERS =====
function editPO(id, supplier, amount, orderDate, expectedDelivery, status, description) {
    document.getElementById('editPOSupplier').value = supplier;
    document.getElementById('editPOAmount').value = amount;
    document.getElementById('editPOOrderDate').value = orderDate;
    document.getElementById('editPOExpectedDelivery').value = expectedDelivery;
    document.getElementById('editPOStatus').value = status;
    document.getElementById('editPODescription').value = description;
    document.getElementById('editPOId').value = id;
    document.getElementById('editPOForm').action = '/procurement/purchase-orders/' + id;
    _openModal('editPOModal');
}

// ===== GOODS RECEIVED NOTES =====
function editGRN(id, supplier, amount, receivedDate, poId, status, notes) {
    document.getElementById('editGRNSupplier').value = supplier;
    document.getElementById('editGRNAmount').value = amount;
    document.getElementById('editGRNDate').value = receivedDate;
    document.getElementById('editGRNPO').value = poId;
    document.getElementById('editGRNStatus').value = status;
    document.getElementById('editGRNNotes').value = notes;
    document.getElementById('editGRNForm').action = '/goods-received-notes/' + id;
    _openModal('editGRNModal');
}

// ===== PAYMENTS =====
function showPaymentReceipt(id, btn) {
    var row = btn.closest('tr');
    document.getElementById('receiptNo').textContent = 'RCT-' + String(id).padStart(4, '0');
    document.getElementById('receiptBillNo').textContent = row.getAttribute('data-bill-no');
    document.getElementById('receiptSupplier').textContent = row.getAttribute('data-supplier');
    document.getElementById('receiptAmount').textContent = '\u20B1' + Number(row.getAttribute('data-amount')).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('receiptDate').textContent = row.getAttribute('data-payment-date');
    document.getElementById('receiptMethod').textContent = row.getAttribute('data-method');
    _openModal('receiptModal');
}

function printReceipt() { window.print(); }

// ===== INIT =====
document.addEventListener('DOMContentLoaded', function() {
    var backdropModals = [
        'viewModal', 'paymentModal', 'poModal', 'editPOModal', 'grnModal',
        'editGRNModal', 'receiptModal', 'editModal'
    ];
    backdropModals.forEach(initBackdropClose);

    document.querySelectorAll('[data-export-pdf]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('PDF EXPORTED!');
        });
    });
});

(function() {
    var fields = { supplier: 'input[name="supplier"]', amount: 'input[name="amount"]' };
    document.querySelectorAll('#poSelect, #grnSelect').forEach(function(select) {
        select.addEventListener('change', function() {
            var opt = this.options[this.selectedIndex];
            Object.keys(fields).forEach(function(key) {
                var el = document.querySelector(fields[key]);
                if (el) el.value = opt.dataset[key] || '';
            });
        });
    });
})();
