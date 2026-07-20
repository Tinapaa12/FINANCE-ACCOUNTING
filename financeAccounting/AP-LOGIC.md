x`# Accounts Payable — Logic Documentation

## Overview
Accounts Payable (AP) tracks money the company owes to suppliers for goods/services received. It's a short-term liability on the balance sheet.

---

## Full Lifecycle: Procure-to-Pay

```
Purchase Order → Goods Received Note → Invoice/Supplier Bill → 3-Way Matching → Approval → Payment → General Ledger
```

---

### 1. Purchase Order (PO)
- Company orders goods from a supplier
- Records: `po_no`, `supplier`, `item_name`, `qty`, `unit_cost`, `amount`
- Status flow: **Draft → Sent → Confirmed → Delivered**
- Model: `PurchaseOrder` in `app/Models/AccountPayable/`

### 2. Goods Received Note (GRN)
- Warehouse confirms goods physically arrived
- Records: `qty_received` vs `qty_ordered` (discrepancy detection)
- Links to PO via `purchase_order_id`
- Auto-creates a Supplier Bill when GRN is Completed
- Model: `GoodsReceivedNote` in `app/Models/AccountPayable/`

### 3. Supplier Bill / Invoice
- Supplier sends the invoice → company now owes money (**liability created**)
- Key fields: `amount`, `due_date`, `status`, `total_paid`, `matching_status`
- Computed: `balance = amount - total_paid`
- Statuses: **Pending → Approved → Paid**
- Tracks: `ewt_rate` (withholding tax), `payment_terms`, `payment_method`
- Model: `SupplierBill` in `app/Models/AccountPayable/`

### 4. 3-Way Matching
- **Critical internal control** — prevents paying for wrong/missing goods
- Compares three documents:
  | Document | What it confirms |
  |----------|-----------------|
  | PO | What was ordered |
  | GRN | What was received |
  | Bill | What supplier is charging |

- Results:
  - **Matched** — all three agree
  - **Partially Matched** — minor discrepancies
  - **Flagged** — significant mismatch, needs human review
  - **Unmatched** — not yet compared

- **A bill CANNOT proceed without being Matched**
- Controller: `MatchingController` in `app/Http/Controllers/Procurement/`

### 5. Approval
- Manager authorizes payment
- Prerequisite: `matching_status === 'Matched'`
- Sets: `approved_at`, `approved_by`, status → `Approved`
- Enforced in: `AccountPayableService::approveBill()`

### 6. Payment
- Three methods to pay:

| Method | Description |
|--------|-------------|
| Single Pay | Mark one bill as Paid via `PATCH /supplier-bills/{id}/pay` |
| Batch Pay | Pay multiple Approved bills at once via `POST /supplier-bills/batch-pay` |
| Manual Payment | Record partial payment with date/reference via `POST /payments` |

- **Business rules enforced:**
  - Bill must be Matched before payment
  - Payment amount cannot exceed bill amount
  - Partial payments accumulate in `total_paid`
  - When `total_paid >= amount`, status auto-flips to `Paid`
  - `paid_at` records actual payment date

- **Journal Entry created on payment:**

  | Account | Debit | Credit |
  |---------|-------|--------|
  | 2100 — Accounts Payable | ✓ (decreases liability) | |
  | 1010 — Cash | | ✓ (decreases asset) |

  Reference format: `PO-2026-{reference}` or fallback `PAY-{bill_no}-{timestamp}`

### 7. General Ledger Impact
- Payment posts a double-entry journal entry:
  - **Debit 2100 AP** → Liability decreases
  - **Credit 1010 Cash** → Asset decreases
- Controllers: `SupplierBillController::pay()`, `PaymentController::store()`
- Service method: `AccountPayableService::createPaymentJournalEntry()`
- Dashboard reflects:
  - "Total Expenses" card sums paid bills
  - Cash Flow chart deducts payments per month
  - Revenue vs Expenses chart includes paid bills

### 8. Overdue & Aging
- Bills past `due_date` with status Pending/Approved = **overdue**
- Dashboard shows:
  - Overdue count and total amount
  - Days past due per bill
  - Upcoming bills grouped by due date proximity (7d, 30d badges)

---

## Key Business Rules Summary

| Rule | Enforced In |
|------|-------------|
| Only Matched bills can be approved | `AccountPayableService::approveBill()` |
| Only Matched bills can be paid | `payBill()`, `recordPayment()` |
| Payment cannot exceed remaining balance | `recordPayment()` |
| Partial payments accumulate | `total_paid += amount` |
| Auto-create bill from GRN completion | `AccountPayableService::completeGrn()` |
| Journal entry: Debit AP, Credit Cash | `createPaymentJournalEntry()` (in both controllers and service) |
| Auto-set Matched on payment | SupplierBillController, PaymentController, batchPay |

---

## File Map

| File | Purpose |
|------|---------|
| `app/Models/AccountPayable/SupplierBill.php` | SupplierBill model with relations |
| `app/Models/AccountPayable/Payment.php` | Payment model |
| `app/Models/AccountPayable/PurchaseOrder.php` | PO model |
| `app/Models/AccountPayable/GoodsReceivedNote.php` | GRN model |
| `app/Models/AccountPayable/Attachment.php` | File attachments (polymorphic) |
| `app/Http/Controllers/AccountPayable/SupplierBillController.php` | Bill CRUD, pay, approve, batchPay |
| `app/Http/Controllers/AccountPayable/PaymentController.php` | Manual payment recording |
| `app/Http/Controllers/Procurement/MatchingController.php` | 3-Way matching logic |
| `app/Http/Controllers/Procurement/GoodsReceiptController.php` | GRN creation/management |
| `app/Http/Controllers/Procurement/PurchaseOrderController.php` | PO creation/management |
| `app/Services/AccountPayableService.php` | Shared business logic (pay, approve, create) |
| `app/Services/DashboardService.php` | KPI/chart data aggregation |
| `routes/web.php` | All AP routes |
| `resources/views/AccountPayable/` | View templates |
| `public/js/account-payable.js` | Frontend payment/modal interactions |
