# Financial Reports — Data Flow & Math Explained

This document explains how each financial report works in this ERP system: where the data comes from, how it flows through the system, and the math behind the numbers.

---

## 1. INCOME STATEMENT (Profit & Loss)

### What it shows
Revenue earned vs Expenses incurred over a period. The bottom line is **Net Income** (profit or loss).

### Data Flow

```
Journal Entries (Posted)
        │
        ▼
  Chart of Accounts
  ┌──────────────┬──────────────┐
  │  Revenue     │  Expense     │
  │  (Type)      │  (Type)      │
  └──────┬───────┴──────┬───────┘
         │              │
         ▼              ▼
  SUM(credit)     SUM(debit)
  per account     per account
         │              │
         └──────┬───────┘
                ▼
         Income Statement
```

### The Math

```
Total Revenue = SUM of all Revenue account credits
Total Expenses = SUM of all Expense account debits
Net Income = Total Revenue - Total Expenses
```

**Key rule:** Revenue accounts have a **normal balance of Credit**, so we sum their `credit` column. Expense accounts have a **normal balance of Debit**, so we sum their `debit` column.

### Example

| Account | Type | Debit | Credit |
|---------|------|-------|--------|
| Sales Revenue | Revenue | 0 | 100,000 |
| Service Revenue | Revenue | 0 | 50,000 |
| Rent Expense | Expense | 20,000 | 0 |
| Salaries Expense | Expense | 60,000 | 0 |
| **Total Revenue** | | | **150,000** |
| **Total Expenses** | | **80,000** | |
| **Net Income** | | | **70,000** |

---

## 2. BALANCE SHEET

### What it shows
What the company **owns** (Assets), **owes** (Liabilities), and the **owner's stake** (Equity) at a specific point in time.

### The Accounting Equation
```
Assets = Liabilities + Equity
```

### Data Flow

```
Journal Entries (Posted)
        │
        ▼
  Chart of Accounts
  ┌──────┬────────┬──────┐
  │Asset │Liability│Equity│
  └──┬───┴───┬────┴──┬───┘
     │       │       │
     ▼       ▼       ▼
  For each account, compute balance:
  
  If normal_balance = Debit:
    Balance = SUM(debit) - SUM(credit)
  
  If normal_balance = Credit:
    Balance = SUM(credit) - SUM(debit)
```

### The Math

```
For Asset accounts (normal balance = Debit):
  Balance = Total Debits - Total Credits

For Liability accounts (normal balance = Credit):
  Balance = Total Credits - Total Debits

For Equity accounts (normal balance = Credit):
  Balance = Total Credits - Total Debits

Then add Retained Earnings:
  Net Income (from Income Statement) → adds to Equity
  If Net Income > 0 → "Retained Earnings"
  If Net Income < 0 → "Retained Earnings (Deficit)"
```

### Example

| Account | Type | Debits | Credits | Balance |
|---------|------|--------|---------|---------|
| Cash | Asset | 500,000 | 200,000 | 300,000 |
| AR | Asset | 100,000 | 30,000 | 70,000 |
| Inventory | Asset | 80,000 | 0 | 80,000 |
| **Total Assets** | | | | **450,000** |
| AP | Liability | 50,000 | 200,000 | 150,000 |
| **Total Liabilities** | | | | **150,000** |
| Owner's Equity | Equity | 0 | 230,000 | 230,000 |
| Retained Earnings | Equity | 0 | 70,000 | 70,000 |
| **Total Equity** | | | | **300,000** |

**Check:** 450,000 (Assets) = 150,000 (Liabilities) + 300,000 (Equity) ✅

---

## 3. BUDGET vs ACTUAL

### What it shows
Compares what you **planned to spend** (budget) against what you **actually spent** (actual from GL), with variance analysis.

### Data Flow

```
┌─────────────────┐     ┌──────────────────────┐
│  BudgetVsActual  │     │  Journal Entry Lines  │
│  (budget_amount) │     │  (actual transactions)│
└────────┬─────────┘     └──────────┬───────────┘
         │                          │
         │                          ▼
         │              ChartOfAccounts (normal_balance)
         │                          │
         └──────────┬───────────────┘
                    ▼
         For each budget row:
         ┌─────────────────────────────┐
         │ actualAmount =              │
         │   if normal_balance=Credit: │
         │     max(credits-debits, 0)  │
         │   else:                     │
         │     max(debits-credits, 0)  │
         │                             │
         │ variance = actual - budget  │
         └─────────────────────────────┘
```

### The Math

```
For each account:
  If account normal_balance = Credit (Revenue):
    Actual = max(Total Credits - Total Debits, 0)
  If account normal_balance = Debit (Expense):
    Actual = max(Total Debits - Total Credits, 0)

Variance = Actual Amount - Budget Amount

Status:
  If variance > 0 AND variance/budget < 5%  → "slightly_over"
  If variance > 0                            → "over" (overspent)
  If variance < 0                            → "under" (underspent)
  If variance = 0                            → "on_budget"
```

### Example

| Account | Budget | Actual | Variance | Status |
|---------|--------|--------|----------|--------|
| Rent Expense | 20,000 | 20,000 | 0 | on_budget |
| Salaries | 55,000 | 60,000 | +5,000 | over |
| Office Supplies | 10,000 | 8,000 | -2,000 | under |
| Marketing | 15,000 | 15,500 | +500 | slightly_over |

---

## 4. CASH FLOW STATEMENT

### What it shows
Money coming **in** (cash receipts) vs money going **out** (cash payments) during a period, and the resulting **net change in cash**.

### Data Flow

```
Journal Entries (Posted)
        │
        ▼
  Find Cash accounts (account_name LIKE 'Cash%')
        │
        ├────────────────────────────────────┐
        ▼                                    ▼
  Cash IN (debits to Cash)             Cash OUT (credits from Cash)
  where counterparty is NOT Cash       where counterparty is Expense/Liability
        │                                    │
        ▼                                    ▼
  Group by counterparty account        Group by counterparty account
        │                                    │
        └────────────────┬───────────────────┘
                         ▼
              Net Cash Flow = Cash In - Cash Out
                         │
                         ▼
              Beginning Cash + Net Cash Flow = Ending Cash
```

### The Math

```
Cash In = SUM of debit entries to Cash accounts
          (where the other side of the journal entry is NOT a Cash account)

Cash Out = SUM of credit entries from Cash accounts
           (where the other side is an Expense or Liability account)

Net Cash Flow = Total Cash In - Total Cash Out

Beginning Cash = Cash balance BEFORE the period starts
                 (SUM(debits) - SUM(credits) for Cash accounts before start date)

Ending Cash = Beginning Cash + Net Cash Flow
```

### Example

| Cash In (Receipts) | Amount |
|--------------------|--------|
| Sales Revenue (received) | 120,000 |
| AR Collections | 30,000 |
| **Total Cash In** | **150,000** |

| Cash Out (Payments) | Amount |
|---------------------|--------|
| Rent (paid) | 20,000 |
| Salaries (paid) | 60,000 |
| Supplier Payments | 40,000 |
| **Total Cash Out** | **120,000** |

| Calculation | Amount |
|-------------|--------|
| Beginning Cash (start of month) | 100,000 |
| + Net Cash Flow (150k - 120k) | +30,000 |
| **= Ending Cash** | **130,000** |

---

## 5. TAX & COMPLIANCE

### What it shows
All tax-related transactions across the system: VAT on sales, VAT on purchases, EWT (Expanded Withholding Tax), and their filing status.

### Data Flow

```
The system collects tax data from 4 sources:
        │
        ├── 1. GL Tax Accounts ──────────────────────────┐
        │     (accounts named "%VAT%" or "%Tax%")        │
        │     Math: taxAmount = credit - debit           │
        │            rate = taxAmount / taxableAmount     │
        │                                                │
        ├── 2. Sales Transactions ───────────────────────┐
        │     (VAT on sales)                             │
        │     Math: taxAmount = total × 0.12 / 1.12      │
        │            (extract VAT from gross amount)     │
        │                                                │
        ├── 3. Purchase Orders ──────────────────────────┐
        │     (VAT on purchases, not yet billed)         │
        │     Math: taxAmount = amount × 12%             │
        │                                                │
        └── 4. Supplier Bills ───────────────────────────┐
              (VAT/EWT on bills)                         │
              Math: taxAmount = amount × ewt_rate / 100  │
```

### The Math

**From GL Tax Accounts:**
```
If account normal_balance = Credit (e.g., VAT Payable):
  Tax Amount = SUM(credit) - SUM(debit)

If account normal_balance = Debit (e.g., Input VAT):
  Tax Amount = SUM(debit) - SUM(credit)

Tax Rate = (Tax Amount / Taxable Amount) × 100
```

**From Sales Transactions (VAT on sales):**
```
VAT Amount = Total Amount × 12% / 112%
           = Total Amount × 0.12 / 1.12
```
This extracts the VAT portion from a gross amount that includes VAT.

**From Purchase Orders (VAT on purchases):**
```
VAT Amount = PO Amount × 12%
```

**From Supplier Bills (EWT/VAT):**
```
Tax Amount = Bill Amount × EWT Rate / 100
```

**Summary:**
```
Total Taxable Amount = SUM of all taxable amounts
Total Tax Amount = SUM of all computed tax amounts
Total Filed = SUM of tax amounts where status = "filed" or "paid"
```

### Example

| Source | Reference | Taxable Amount | Rate | Tax Amount | Status |
|--------|-----------|---------------|------|------------|--------|
| Sales Transaction | INV-001 | 112,000 | 12% | 12,000 | filed |
| Purchase Order | PO-001 | 50,000 | 12% | 6,000 | pending |
| Supplier Bill | BILL-001 | 30,000 | 1% (EWT) | 300 | filed |
| GL Journal Entry | JE-005 | 10,000 | 12% | 1,200 | pending |
| **Totals** | | **202,000** | | **19,500** | **12,300 filed** |

---

## HOW IT ALL CONNECTS

```
                    ┌─────────────────────────┐
                    │  Daily Transactions     │
                    │  (Sales, Bills, POs,    │
                    │   Payments, Receipts)   │
                    └───────────┬─────────────┘
                                │
                                ▼
                    ┌─────────────────────────┐
                    │  Journal Entries (GL)   │
                    │  Double-entry:          │
                    │  Debits = Credits       │
                    └───────┬─────────────────┘
                            │
          ┌─────────────────┼──────────────────┐
          │                 │                  │
          ▼                 ▼                  ▼
  ┌───────────────┐ ┌──────────────┐ ┌────────────────┐
  │ Income        │ │ Balance      │ │ Cash Flow      │
  │ Statement     │ │ Sheet        │ │ Statement      │
  │               │ │              │ │                │
  │ Revenue       │ │ Assets       │ │ Cash In        │
  │ - Expenses    │ │ = Liabilities│ │ - Cash Out     │
  │ = Net Income  │ │   + Equity   │ │ = Net Cash Flow│
  └───────┬───────┘ └──────┬───────┘ └───────┬────────┘
          │                │                  │
          └────────────────┼──────────────────┘
                           │
                           ▼
                  ┌────────────────┐
                  │ Budget vs      │
                  │ Actual         │
                  │ (planned vs    │
                  │  actual spend) │
                  └────────────────┘

  Tax & Compliance reads from:
  ┌─── GL Tax Accounts
  ├─── Sales Transactions
  ├─── Purchase Orders
  └─── Supplier Bills
```

### The Golden Rule of Double-Entry Accounting

Every transaction affects **at least two accounts**:

| Transaction | Debit | Credit |
|-------------|-------|--------|
| Sale on credit | AR (Asset) | Revenue |
| Receive cash payment | Cash (Asset) | AR (Asset) |
| Pay supplier bill | AP (Liability) | Cash (Asset) |
| Record expense | Expense | AP (Liability) |

**Debits must always equal Credits.** This is what keeps the Balance Sheet balanced and the Income Statement accurate.