# Front End & UI Design — Financial Reporting & Tax Compliance

## 1. Design System Overview

- **Framework**: Laravel Blade templating with Tailwind CSS utility classes
- **JavaScript**: Alpine.js for interactivity (dropdowns, modals), Chart.js for dashboard charts
- **Layout**: Fixed sidebar + scrollable content area, fully responsive down to tablet
- **Color palette**: Slate-900 sidebar, white content cards, green/red/amber accent sections

## 2. Sidebar Navigation

**File**: `layouts/app.blade.php`

The left sidebar is a fixed 256px-wide dark panel (bg-slate-900) containing collapsible sections:

| Section | Items |
|---------|-------|
| Main | Dashboard |
| Sales | New Transaction |
| General Ledger | Chart of Accounts, Journal Entries |
| Account Payables | Supplier Bills, Payments Made |
| Account Receivables | A/R Overview, Payments Received, Aging Report |
| **Reports** | **Financial Reports**, **Tax and Compliance**, **Manage Data**, Audit Trail |
| Settings | Payment Methods |

**Active state**: Each nav item uses `request()->routeIs(...)` to apply a `bg-blue-600 text-white` highlight on the current page. The Financial Reports item stays highlighted across all four report sub-pages (Income, Balance Sheet, Budget vs Actual, Cash Flow).

## 3. Report Sub-Navigation Tabs

When on any `reports.*` route, a horizontal tab bar appears below the header:

```
[Income Statements] [Balance Sheet] [Budget vs Actual] [Cash Flow]
```

Active tab gets `bg-blue-600 text-white`, inactive tabs are `text-gray-600`. This gives users quick access to switch between reports without going back to the sidebar.

## 4. Period Selector (All Report Pages)

Every report page includes a top-right **period dropdown** populated from the `financial_reports` table:

- Default: "All periods" (shows all data, no date filter)
- Options: Each distinct report period formatted as "F Y" (e.g., "June 2024")
- Behavior: `onchange="window.location.href='?report_id='+this.value"` — page reloads with the selected period
- When a period is selected, the controller applies `whereBetween('transaction_date', [$start, $end])` to journal entry queries

## 5. Income Statement
`financial-reporting/reports/income.blade.php`

**Layout**: Two-column responsive grid (`flex-col lg:flex-row gap-6`)

**Left column** — Income Statement card:
- Header with title + period label
- **Revenue Earned** section (green-50 background): Lists each revenue account with its total credit amount, summed as "Total Revenue"
- **Expenses** section (red-50 background): Lists each expense account with its total debit amount, summed as "Total Expenses"

**Right column** — Trial Balance card (w-96):
- Table with columns: Account | Debit | Credit
- Shows all chart of accounts (including zero-balance accounts)
- Footer row: Totals (debits must equal credits)

**Empty state**: Yellow banner "No data yet. Add journal entries with Revenue and Expense accounts first."

## 6. Balance Sheet
`financial-reporting/reports/assets.blade.php`

**Layout**: Two-column responsive grid

**Left column** — Assets card:
- Blue-50 background rows for each asset account
- "Total Assets" at bottom

**Right column** — Liabilities & Equity card:
- **Liabilities** subsection (purple-50): Each liability account with amount (balance = credit - debit for Credit-normal accounts)
- **Equity** subsection (amber-50): Existing equity accounts + **Retained Earnings** (auto-computed as Net Income = Revenue - Expenses)
- **Verification row**: "Liabilities + Equity" total (bold, top-border-2) — must equal Total Assets

**Accounting equation enforcement**: Balance computed per account using `normal_balance`: Debit-normal → debit - credit, Credit-normal → credit - debit. Only positive balances shown. Retained Earnings added automatically to balance the equation.

**Seeded data example** (June 2024):
- Assets: Cash in Bank ₱41,500 + AR ₱212,000 = **₱253,500**
- Liabilities: AP ₱25,000 + VAT ₱12,000 = **₱37,000**
- Retained Earnings: Revenue ₱250,000 - Expenses ₱33,500 = **₱216,500**
- **₱253,500 = ₱37,000 + ₱216,500** ✓

## 7. Cash Flow Statement
`financial-reporting/reports/cashflow.blade.php`

**Layout**: Two-column grid

**Left column** — Cash Flow breakdown:
- **Cash In** section (green-50): Actual cash received — debits to Cash accounts where the other side is NOT cash (e.g., collections from customers). Empty state: "No cash inflows this period."
- **Cash Out** section (red-50): Actual cash paid — credits to Cash accounts where the other side is an Expense or Liability (e.g., bill payments, expense payments). Empty state: "No cash outflows this period."

**Right column** — Cash Summary card:
- Beginning Cash Balance
- + Cash In (green text)
- - Cash Out (red text)
- = Net Cash Flow (+ or - with color)
- **Ending Cash Balance** (large 2xl bold, thick top border)

## 8. Budget vs Actual
`financial-reporting/reports/liabilities.blade.php`

**Single card** with table layout:

| Account | Budget | Actual | Variance | Status |
|---------|--------|--------|----------|--------|
| Account name | ₱X | ₱X | +₱X or -₱X | Badge |

**Variance coloring**: Red for positive (over budget), green for negative (under budget)
**Status badges**: Over (red), Slightly Over (yellow), Under (green), On Budget (gray)
**Empty state**: Yellow banner with instructions to add budget targets via Manage Data

## 9. Tax and Compliance
`financial-reporting/tax/compliance.blade.php`

**Layout**: Single column with three summary cards:

| Card 1 | Card 2 | Card 3 |
|--------|--------|--------|
| Total Taxable Amount (₱) | Total Tax Computed (₱) | Total Tax Paid/Filed (₱) |

**Data table**: Reference | Tax Type | Taxable Amount | Rate | Tax Amount | Status

**Status colors**: Filed → blue, Paid → green, Pending → yellow
**Period filter**: Dropdown populated from posted journal entry dates

**Data source**: Auto-computed from General Ledger. The controller finds accounts whose name contains "VAT", "EWT", "Tax", or "Withholding", then queries their balances from posted journal entries sorted by transaction period.

**Example** (from seeded data): Sales to Customer C with VAT
- Journal Entry: JE-2024-00130
- Tax type: VAT
- Taxable amount: ₱100,000 (Sales Revenue in the same entry)
- Computed tax: ₱12,000 (Output VAT Payable credit)
- Rate: 12% (auto-calculated)

**Filing status**: Defaults to Pending. To mark as Filed or Paid, use Manage Data → Tax Records tab to create an override record matching the same reference.

**Empty state**: "No tax transactions this period. Add journal entries with VAT/Tax accounts first."

## 10. Manage Data Page
`financial-reporting/manage/index.blade.php`

**Now simplified** — only shows tabs that still work with GL-based reporting:

| Tab | Purpose |
|-----|---------|
| Budget vs Actual | Set budget targets (actuals auto-computed from GL) |
| Tax Records | Filing status overrides — match a reference (e.g. Journal Entry #5) to mark it Filed or Paid. Tax amounts auto-computed from GL. |

**Removed tabs**: Income Statement, Trial Balance, Balance Sheet, Cash Flow — those wrote to orphan tables not read by the GL-powered reports. To add report data, use Chart of Accounts and Journal Entries instead.

**Banner**: Blue info box explains that all reports are auto-computed from the General Ledger.

## 11. PDF Export

**Layout**: `layouts/pdf.blade.php`

- Uses `html2pdf.js` library for client-side PDF generation
- A4 portrait orientation, 0.5in margins
- Each PDF has: company name, generation timestamp, title, content area, and footer disclaimer
- **Close** button to return, **Download** button to trigger save
- Print mode (`?print=1`) triggers browser's native print dialog

## 12. Data Flow Summary

```
User creates accounts → Chart of Accounts
User posts entries  → Journal Entries (General Ledger)
                       │
                       ▼
Financial Reports ─── reads from ─── journal_entry_lines (General Ledger)
  ├── Income Statement (Revenue credits, Expense debits)
  ├── Balance Sheet (Asset/Liability/Equity balances per normal_balance + Retained Earnings from Net Income)
  ├── Cash Flow (actual cash movements: Cash DRs = Cash In, Cash CRs against Expense/Liability = Cash Out)
  ├── Budget vs Actual (targets from budget_vs_actuals + actuals from JE)
  └── Tax Compliance (VAT/tax account balances from GL + filing status overrides from tax_records)

Manage Data ─────── writes to ─── financial_reports + report-specific tables
```

---

# How AR and AP Use Financial Reports

## For Accounts Receivable Staff

### Daily / Weekly Tasks

| Report | What AR Looks For | Why It Matters |
|--------|-------------------|----------------|
| **Income Statement** | Revenue accounts — are sales being recorded correctly? | Ensures invoices posted to GL match actual sales |
| **Balance Sheet** | Accounts Receivable balance under Assets | Confirms total outstanding receivables matches AR subledger |
| **Cash Flow** | Cash In from Revenue (collections) | Tracks how much AR collected converted to cash |
| **Trial Balance** | Accounts Receivable debit/credit totals | Verifies AR control account balances |

### Real-World Flow

1. AR creates a Sales Transaction → system auto-posts JE: DR Accounts Receivable, CR Sales Revenue
2. Customer pays → AR records payment → system auto-posts JE: DR Cash, CR Accounts Receivable
3. AR manager opens **Balance Sheet** → sees Accounts Receivable balance decrease
4. AR manager opens **Income Statement** → sees Sales Revenue increase
5. AR manager opens **Cash Flow** → sees Cash In from Revenue

**Key question AR answers**: "Are we collecting on time?" → Cash In vs. Sales Revenue

## For Accounts Payable Staff

### Daily / Weekly Tasks

| Report | What AP Looks For | Why It Matters |
|--------|-------------------|----------------|
| **Balance Sheet** | Accounts Payable balance under Liabilities | Confirms total unpaid bills matches AP subledger |
| **Cash Flow** | Cash Out from Expenses (payments made) | Tracks cash going out to suppliers |
| **Income Statement** | Expense accounts — are costs being recorded? | Ensures supplier bills posted to correct expense accounts |
| **Trial Balance** | Accounts Payable debit/credit totals | Verifies AP control account balances |

### Real-World Flow

1. AP receives supplier bill → enters in Supplier Bills → system auto-posts JE: DR Expense, CR Accounts Payable
2. AP processes payment → system auto-posts JE: DR Accounts Payable, CR Cash
3. AP manager opens **Balance Sheet** → sees Accounts Payable balance
4. AP manager opens **Cash Flow** → sees Cash Out matching payments made
5. AP manager opens **Budget vs Actual** → compares actual expenses against budget

**Key question AP answers**: "Are we staying within budget?" → Budget vs Actual report

## Shared Reports (Both AR and AP)

| Report | How Both Use It |
|--------|-----------------|
| **Trial Balance** | Verify all accounts are in balance before closing the period |
| **Cash Flow** | Understand total cash position — inflows (AR) vs outflows (AP) |
| **Income Statement** | Overall profitability — revenue (AR's work) minus expenses (AP's work) |

## Period-End Close Process

```
                AR Team                           AP Team
                  │                                 │
        Generate Sales Report              Generate AP Aging
        Post Revenue JEs                   Post Accrual JEs
                  │                                 │
                  └──────────┬──────────────────────┘
                             │
                    Financial Reports
                  (Controller / Accountant)
                             │
              ┌──────────────┼──────────────┐
              │              │              │
        Income Stmt     Balance Sheet    Cash Flow
        (Profit/Loss)   (Financial Pos)  (Cash Movement)
              │              │              │
              └──────────────┼──────────────┘
                             │
                       Tax Compliance
                      (VAT, EWT, etc.)
```
