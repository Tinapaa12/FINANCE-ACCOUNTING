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
    </style>
    @yield('head')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-white flex flex-col flex-shrink-0">
            <div class="p-6 flex items-center gap-3">
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

            <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
                <x-sidebar-section title="Main">
                    <x-sidebar-nav-item 
                        href="{{ route('dashboard') }}" 
                        :active="request()->routeIs('dashboard')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>'>
                        Dashboard
                    </x-sidebar-nav-item>
                </x-sidebar-section>

                <!-- Sales — dummy module simulating an external ERP Sales module -->
                <x-sidebar-section title="Sales">
                    <x-sidebar-nav-item
                        href="{{ route('sales-transactions.create') }}"
                        :active="request()->routeIs('sales-transactions.*')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />'>
                        New Transaction
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

                <!-- Account Payables - left blank per user request -->
                <x-sidebar-section title="Account Payables">
                    <x-sidebar-nav-item href="javascript:void(0)"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'>
                        Supplier Bills
                    </x-sidebar-nav-item>
                    <x-sidebar-nav-item href="javascript:void(0)"
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
                        :active="request()->routeIs('reports.income')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'>
                        Financial Reports
                    </x-sidebar-nav-item>
                    <x-sidebar-nav-item 
                        href="{{ route('tax.compliance') }}" 
                        :active="request()->routeIs('tax.compliance')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'>
                        Tax and Compliance
                    </x-sidebar-nav-item>
                    <x-sidebar-nav-item 
                        href="{{ route('reports.manage') }}" 
                        :active="request()->routeIs('reports.manage')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>'>
                        Manage Data
                    </x-sidebar-nav-item>
                </x-sidebar-section>

            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">@yield('page-title', trim($__env->yieldContent('page-heading')) ?: 'Dashboard')</h2>
                <div class="flex items-center gap-3">
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
                           class="border px-3 py-1.5 rounded text-sm font-medium hover:bg-gray-50 transition-colors">VIEW PDF</a>
                        <a href="{{ route($currentPdfRoute, ['print' => 1]) }}" target="_blank" class="border px-3 py-1.5 rounded text-sm font-medium hover:bg-gray-50 transition-colors">PRINT</a>
                    @endif
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 cursor-pointer">
                            <div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div class="text-left">
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

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-8">
                @if(request()->routeIs('reports.*'))
                    <div class="inline-flex bg-white rounded-lg border p-1 mb-6">
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
    @yield('scripts')
    @stack('scripts')
</body>
</html>