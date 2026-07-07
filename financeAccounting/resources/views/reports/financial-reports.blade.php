{{--
    resources/views/reports/financial-reports.blade.php

    Route example (routes/web.php):
        Route::get('/reports/financial', [FinancialReportController::class, 'index'])->name('reports.financial');

    Controller example (app/Http/Controllers/FinancialReportController.php):
        public function index() {
            return view('reports.financial-reports', [
                'company' => 'Murana',
                'month' => 'September',
                'revenue' => [
                    ['label' => 'Sales revenue', 'amount' => 520000],
                    ['label' => 'Service revenue', 'amount' => 45000],
                ],
                'expenses' => [
                    ['label' => 'Cost of good', 'amount' => 280000],
                    ['label' => 'Salaries & Wages', 'amount' => 135000],
                    ['label' => 'Rent Expense', 'amount' => 15000],
                    ['label' => 'Utilities', 'amount' => 8200],
                    ['label' => 'Marketing', 'amount' => 18400],
                    ['label' => 'Office Supplies', 'amount' => 5000],
                ],
                'trialBalance' => [
                    ['account' => 'Cash in bank', 'credit' => 312400, 'debit' => null],
                    ['account' => 'Accounts Recievable', 'credit' => 248500, 'debit' => null],
                    ['account' => 'Accounts Payable', 'credit' => null, 'debit' => 91200],
                    ['account' => 'Sales Revenue', 'credit' => null, 'debit' => 565500],
                    ['account' => 'Total Expenses', 'credit' => 461600, 'debit' => null],
                ],
            ]);
        }
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Financial Reports</title>
    {{-- For production, install Tailwind via npm + Vite instead of the CDN below --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="flex h-screen overflow-hidden">

    {{-- ============ SIDEBAR ============ --}}
    <aside class="w-64 bg-slate-900 text-slate-300 flex-shrink-0 overflow-y-auto">
        <div class="flex items-center gap-3 px-5 py-5 border-b border-slate-800">
            <div class="bg-blue-600 text-white rounded-md p-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 018 0v2m-4-9a4 4 0 100-8 4 4 0 000 8z" />
                </svg>
            </div>
            <span class="text-white font-semibold leading-tight">Finance and<br>Accounting</span>
        </div>

        <nav class="px-3 py-4 space-y-6 text-sm">
            <div>
                <p class="uppercase text-xs text-slate-500 px-2 mb-2">Main</p>
                <a href="#" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-slate-800">
                    <span>🏠</span> Dashboard
                </a>
            </div>

            <div>
                <p class="uppercase text-xs text-slate-500 px-2 mb-2">General Ledger</p>
                <a href="#" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-slate-800">📊 Chart of Accounts</a>
                <a href="#" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-slate-800">📓 Journal Entries</a>
            </div>

            <div>
                <p class="uppercase text-xs text-slate-500 px-2 mb-2">Account Payables</p>
                <a href="#" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-slate-800">🧾 Supplier Bills</a>
                <a href="#" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-slate-800">💳 Payments Made</a>
            </div>

            <div>
                <p class="uppercase text-xs text-slate-500 px-2 mb-2">Account Receivables</p>
                <a href="#" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-slate-800">📄 A/R Overview</a>
                <a href="#" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-slate-800">💰 Payments Received</a>
            </div>

            <div>
                <p class="uppercase text-xs text-slate-500 px-2 mb-2">Reports</p>
                <a href="#" class="flex items-center gap-2 px-2 py-2 rounded bg-blue-600 text-white">📈 Financial Reports</a>
                <a href="#" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-slate-800">🧮 Tax and Compliance</a>
            </div>
        </nav>
    </aside>

    {{-- ============ MAIN CONTENT ============ --}}
    <div class="flex-1 flex flex-col overflow-y-auto">

        {{-- Top bar --}}
        <header class="flex items-center justify-between bg-white px-6 py-4 border-b">
            <h1 class="text-xl font-semibold">Financial Reports</h1>
            <div class="flex items-center gap-4">
                <button class="border px-3 py-1.5 rounded text-sm font-medium hover:bg-gray-50">EXPORT PDF</button>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white">👤</div>
                    <div class="text-sm leading-tight">
                        <p class="font-medium">Admin User</p>
                        <p class="text-gray-400 text-xs">Administrator</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6 flex flex-col lg:flex-row gap-6">

            {{-- LEFT: Income Statement --}}
            <div class="flex-1">

                {{-- Tabs --}}
                <div class="inline-flex bg-white rounded-lg border p-1 mb-4">
                    <button class="px-4 py-1.5 rounded-md bg-blue-600 text-white text-sm font-medium">Income Statements</button>
                    <button class="px-4 py-1.5 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50">Assets</button>
                    <button class="px-4 py-1.5 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50">Liabilities</button>
                </div>

                <div class="bg-white rounded-lg border p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-lg">Income statements</h2>
                        <select class="border rounded px-3 py-1.5 text-sm">
                            <option>{{ $month ?? 'September' }}</option>
                        </select>
                    </div>

                    {{-- Revenue --}}
                    <div class="bg-green-50 rounded-lg p-4 mb-4">
                        <p class="font-semibold text-green-900 mb-2">Revenue Earned</p>
                        @php $totalRevenue = 0; @endphp
                        @foreach($revenue as $item)
                            @php $totalRevenue += $item['amount']; @endphp
                            <div class="flex justify-between text-sm text-green-900 py-1">
                                <span>{{ $item['label'] }}</span>
                                <span>₱{{ number_format($item['amount']) }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between font-semibold text-green-900 mt-3 pt-3 border-t border-green-200">
                            <span>Total Revenue</span>
                            <span>₱{{ number_format($totalRevenue) }}</span>
                        </div>
                    </div>

                    {{-- Expenses --}}
                    <div class="bg-red-50 rounded-lg p-4">
                        <p class="font-semibold text-red-900 mb-2">Expenses</p>
                        @php $totalExpenses = 0; @endphp
                        @foreach($expenses as $item)
                            @php $totalExpenses += $item['amount']; @endphp
                            <div class="flex justify-between text-sm text-red-900 py-1">
                                <span>{{ $item['label'] }}</span>
                                <span>₱{{ number_format($item['amount']) }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between font-semibold text-red-900 mt-3 pt-3 border-t border-red-200">
                            <span>Total expenses</span>
                            <span>₱{{ number_format($totalExpenses) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Trial Balance --}}
            <div class="w-full lg:w-96">
                <div class="bg-white rounded-lg border p-5">
                    <h2 class="font-semibold text-lg mb-4">Trial balance</h2>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2 font-medium">Account</th>
                                <th class="py-2 font-medium">Credit</th>
                                <th class="py-2 font-medium">Debit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalCredit = 0; $totalDebit = 0; @endphp
                            @foreach($trialBalance as $row)
                                @php
                                    $totalCredit += $row['credit'] ?? 0;
                                    $totalDebit += $row['debit'] ?? 0;
                                @endphp
                                <tr class="border-b last:border-0">
                                    <td class="py-2">{{ $row['account'] }}</td>
                                    <td class="py-2">{{ $row['credit'] ? '₱'.number_format($row['credit']) : '--' }}</td>
                                    <td class="py-2">{{ $row['debit'] ? '₱'.number_format($row['debit']) : '--' }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-semibold">
                                <td class="py-2">Totals</td>
                                <td class="py-2">₱{{ number_format($totalCredit) }}</td>
                                <td class="py-2">₱{{ number_format($totalDebit) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</div>

</body>
</html>
