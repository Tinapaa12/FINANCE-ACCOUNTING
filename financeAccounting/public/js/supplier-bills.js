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

function editBill(id, supplier, amount, dueDate, status)
{
    document.getElementById('editSupplier').value = supplier;
    document.getElementById('editAmount').value = amount;
    document.getElementById('editDue').value = dueDate;
    document.getElementById('editStatus').value = status;

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
// ---------- GLOBAL ----------

window.openModal = openModal;
window.closeModal = closeModal;
window.viewBill = viewBill;
window.closeViewModal = closeViewModal;
window.editBill = editBill;
window.closeEditModal = closeEditModal;
