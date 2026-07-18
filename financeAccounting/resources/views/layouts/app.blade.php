<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Finance and Accounting')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .bg-dot-grid { background-image: radial-gradient(circle, #cbd5e1 1px, transparent 1px); background-size: 22px 22px; }
        .content-scroll::-webkit-scrollbar-track { background: #f1f5f9; } .content-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; }
        [id$="Modal"].active{display:flex!important}
    </style>
    @yield('head')
</head>
<body class="bg-gray-100">
    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-20 lg:hidden" x-cloak></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-white flex flex-col flex-shrink-0 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:z-auto">
            <div class="p-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg leading-tight">Finance and</h1>
                        <h1 class="font-bold text-lg leading-tight">Accounting</h1>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
                <x-sidebar-section title="Main">
                    <x-sidebar-nav-item
                        href="{{ route('dashboard') }}"
                        :active="request()->routeIs('dashboard')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>'>
                        Dashboard
                    </x-sidebar-nav-item>
                </x-sidebar-section>

                <x-sidebar-section title="Procurement">
                    <x-sidebar-nav-item
                        href="{{ route('procurement.po.index') }}"
                        :active="request()->routeIs('procurement.po*')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002 2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>'>
                        Purchase Orders
                    </x-sidebar-nav-item>
                    <x-sidebar-nav-item
                        href="{{ route('procurement.gr.index') }}"
                        :active="request()->routeIs('procurement.gr*')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>'>
                        Goods Receipts
                    </x-sidebar-nav-item>
                    <x-sidebar-nav-item
                        href="{{ route('procurement.matching.index') }}"
                        :active="request()->routeIs('procurement.matching*')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2M12 3v4m-6 4h12M5 11h14a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2z"></path>'>
                        3-Way Matching
                    </x-sidebar-nav-item>
                </x-sidebar-section>

                <x-sidebar-section title="Dummy">
                    <x-sidebar-nav-item
                        href="{{ route('sales-transactions.create') }}"
                        :active="request()->routeIs('sales-transactions.*')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />'>
                        New Transaction
                    </x-sidebar-nav-item>
                    <x-sidebar-nav-item
                        href="{{ route('reports.manage') }}"
                        :active="request()->routeIs('reports.manage')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>'>
                        Manage Data
                    </x-sidebar-nav-item>
                </x-sidebar-section>

                <x-sidebar-section title="General Ledger">
                    <x-sidebar-nav-item
                        href="{{ route('chart-of-accounts.index') }}"
                        :active="request()->routeIs('chart-of-accounts.*')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'>
                        Chart of Accounts
                    </x-sidebar-nav-item>
                    <x-sidebar-nav-item
                        href="{{ route('journal-entries.index') }}"
                        :active="request()->routeIs('journal-entries.*')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>'>
                        Journal Entries
                    </x-sidebar-nav-item>
                </x-sidebar-section>

                <x-sidebar-section title="Account Payables">
                    <x-sidebar-nav-item
                        href="{{ route('supplier-bills.index') }}"
                        :active="request()->routeIs('supplier-bills*') || request()->routeIs('purchase-orders*') || request()->routeIs('goods-received-notes*')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'>
                        Supplier Bills
                    </x-sidebar-nav-item>
                    <x-sidebar-nav-item
                        href="{{ route('payments.index') }}"
                        :active="request()->routeIs('payments*')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>'>
                        Payments Made
                    </x-sidebar-nav-item>
                </x-sidebar-section>

                <x-sidebar-section title="Account Receivables">
                    <x-sidebar-nav-item
                        href="{{ route('ar.overview') }}"
                        :active="request()->routeIs('ar.overview')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'>
                        A/R Overview
                    </x-sidebar-nav-item>
                    <x-sidebar-nav-item
                        href="{{ route('ar.payments') }}"
                        :active="request()->routeIs('ar.payments')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>'>
                        Payments Received
                    </x-sidebar-nav-item>
                    <x-sidebar-nav-item
                        href="{{ route('ar.aging') }}"
                        :active="request()->routeIs('ar.aging')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2M12 3v4m-6 4h12M5 11h14a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2z"></path>'>
                        Aging Report
                    </x-sidebar-nav-item>
                </x-sidebar-section>

                <x-sidebar-section title="Reports">
                    <x-sidebar-nav-item
                        href="{{ route('reports.income') }}"
                        :active="request()->routeIs('reports.*') && !request()->routeIs('reports.manage*')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'>
                        Financial Reports
                    </x-sidebar-nav-item>
                    <x-sidebar-nav-item
                        href="{{ route('tax.compliance') }}"
                        :active="request()->routeIs('tax.compliance')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'>
                        Tax and Compliance
                    </x-sidebar-nav-item>
                </x-sidebar-section>

            </nav>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">

            <header class="bg-white border-b border-gray-200 px-4 sm:px-8 py-4 flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 truncate">@yield('page-title', trim($__env->yieldContent('page-heading')) ?: 'Dashboard')</h2>
                <div class="flex items-center gap-2 sm:gap-3 ml-auto">
                    @php
                        $pdfRoutes = [
                            'reports.income'         => 'reports.income.pdf',
                            'reports.assets'         => 'reports.assets.pdf',
                            'reports.liabilities'    => 'reports.liabilities.pdf',
                            'reports.cashflow'       => 'reports.cashflow.pdf',
                            'tax.compliance'         => 'tax.compliance.pdf',
                            'chart-of-accounts.index' => 'chart-of-accounts.pdf',
                            'journal-entries.index'  => 'journal-entries.pdf',
                        ];
                        $currentPdfRoute = null;
                        foreach ($pdfRoutes as $pageRoute => $pdfRoute) {
                            if (request()->routeIs($pageRoute)) {
                                $currentPdfRoute = $pdfRoute;
                            }
                        }
                    @endphp
                    @if($currentPdfRoute)
                        <a href="{{ route($currentPdfRoute) }}" target="_blank"
                           class="hidden sm:inline-flex border px-3 py-1.5 rounded text-sm font-medium hover:bg-gray-50 transition-colors">VIEW PDF</a>
                        <a href="{{ route($currentPdfRoute, ['print' => 1]) }}" target="_blank" class="hidden sm:inline-flex border px-3 py-1.5 rounded text-sm font-medium hover:bg-gray-50 transition-colors">PRINT</a>
                    @endif
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 cursor-pointer">
                            <div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div class="text-left hidden sm:block">
                                <p class="text-sm font-semibold text-gray-900">{{ session('auth_username', 'Admin User') }}</p>
                                <p class="text-xs text-gray-500">Administrator</p>
                            </div>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 sm:p-8">
                @if(request()->routeIs('reports.*'))
                    <div class="flex flex-nowrap sm:flex-wrap overflow-x-auto bg-white rounded-lg border p-1 mb-6 gap-1">
                        <a href="{{ route('reports.income') }}"
                           class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors
                                  {{ request()->routeIs('reports.income') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                            Income Statements
                        </a>
                        <a href="{{ route('reports.assets') }}"
                           class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors
                                   {{ request()->routeIs('reports.assets') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                            Balance Sheet
                        </a>
                        <a href="{{ route('reports.liabilities') }}"
                           class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors
                                   {{ request()->routeIs('reports.liabilities') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                            Budget vs Actual
                        </a>
                        <a href="{{ route('reports.cashflow') }}"
                           class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors
                                  {{ request()->routeIs('reports.cashflow') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                            Cash Flow
                        </a>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50 p-4" style="display:none;">
        <div class="w-full max-w-[500px] bg-white rounded-xl shadow-xl p-6 sm:p-8 max-h-[90vh] overflow-y-auto" id="receiptContent">
            <div class="text-center border-b border-gray-200 pb-4 mb-4">
                <h2 class="text-2xl font-bold">RECEIPT</h2>
                <p class="text-sm text-gray-500">Payment Receipt</p>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Receipt No.</span><span id="receiptNo" class="text-gray-900 font-semibold"></span></div>
                <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Bill No.</span><span id="receiptBillNo" class="text-gray-900"></span></div>
                <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">PO No.</span><span id="receiptPoNo" class="text-gray-900"></span></div>
                <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">GRN No.</span><span id="receiptGrnNo" class="text-gray-900"></span></div>
                <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Supplier</span><span id="receiptSupplier" class="text-gray-900"></span></div>
                <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Amount</span><span id="receiptAmount" class="text-gray-900 font-bold text-lg"></span></div>
                <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Payment Method</span><span id="receiptPaymentMethod" class="text-gray-900"></span></div>
                <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Date Paid</span><span id="receiptDatePaid" class="text-gray-900"></span></div>
                <div class="flex justify-between border-b border-gray-100 pb-2"><span class="font-medium text-gray-600">Status</span><span id="receiptStatus" class="text-green-600 font-semibold"></span></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="printReceipt()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Print</button>
                <button type="button" onclick="closeReceiptModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Close</button>
            </div>
        </div>
    </div>

    <style>
    @media print {
        body * { visibility: hidden; }
        #receiptContent, #receiptContent * { visibility: visible; }
        #receiptContent { position: fixed; left: 0; top: 0; width: 100%; padding: 40px; box-shadow: none; }
        #receiptContent .flex.justify-end.gap-3 { display: none !important; }
    }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-export-pdf]').forEach(function (btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    alert('PDF EXPORTED!');
                });
            });
        });
    </script>
    <script src="{{ asset('js/account-payable.js') }}"></script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>