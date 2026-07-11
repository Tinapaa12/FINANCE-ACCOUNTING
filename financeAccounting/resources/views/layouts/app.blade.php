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

    {{-- ============ SIDEBAR ============ --}}
    <aside class="w-64 bg-slate-900 text-white flex flex-col flex-shrink-0 overflow-y-auto">
        <div class="p-6 flex items-center gap-3 border-b border-slate-800">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-lg leading-tight">Finance and</h1>
                <h1 class="font-bold text-lg leading-tight">Accounting</h1>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 overflow-y-auto">

            <x-sidebar-section title="General Ledger">
                <x-sidebar-nav-item
                    href="javascript:void(0)"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V9m3 8V5m3 12v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />'>
                    Chart of Accounts
                </x-sidebar-nav-item>
                <x-sidebar-nav-item
                    href="javascript:void(0)"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />'>
                    Journal Entries
                </x-sidebar-nav-item>
            </x-sidebar-section>

            <x-sidebar-section title="Account Payables">
                <x-sidebar-nav-item
                    href="javascript:void(0)"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'>
                    Supplier Bills
                </x-sidebar-nav-item>
                <x-sidebar-nav-item
                    href="javascript:void(0)"
                    icon='<rect x="2" y="6" width="20" height="13" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 10h20M6 15h4" />'>
                    Payments Made
                </x-sidebar-nav-item>
            </x-sidebar-section>

            <x-sidebar-section title="Account Receivables">
                <x-sidebar-nav-item
                    href="{{ route('ar.overview') }}"
                    :active="request()->routeIs('ar.overview')"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'>
                    A/R Overview
                </x-sidebar-nav-item>
                <x-sidebar-nav-item
                    href="{{ route('ar.payments') }}"
                    :active="request()->routeIs('ar.payments')"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v2m9-8a9 9 0 11-18 0 9 9 0 0118 0z" />'>
                    Payments Received
                </x-sidebar-nav-item>
                <x-sidebar-nav-item
                    href="{{ route('ar.aging') }}"
                    :active="request()->routeIs('ar.aging')"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2M12 3v4m-6 4h12M5 11h14a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2z" />'>
                    Aging Report
                </x-sidebar-nav-item>
            </x-sidebar-section>

            <x-sidebar-section title="Reports">
                <x-sidebar-nav-item
                    href="{{ route('reports.income') }}"
                    :active="request()->routeIs('reports.*')"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l7 7-7 7zM3 5v14M9 19h9a2 2 0 002-2V7a2 2 0 00-2-2H9" />'>
                    Financial Reports
                </x-sidebar-nav-item>
                <x-sidebar-nav-item
                    href="{{ route('tax.compliance') }}"
                    :active="request()->routeIs('tax.compliance')"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'>
                    Tax and Compliance
                </x-sidebar-nav-item>
            </x-sidebar-section>

        </nav>
    </aside>

    {{-- ============ MAIN CONTENT ============ --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top Header --}}
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between flex-shrink-0">
            <h2 class="text-xl font-bold text-gray-900">
                @yield('page-title', trim($__env->yieldContent('page-heading')) ?: 'Financial Reports')
            </h2>
            <div class="flex items-center gap-4">
                @php
                    $pdfRoutes = [
                        'reports.income'      => 'reports.income.pdf',
                        'reports.assets'      => 'reports.assets.pdf',
                        'reports.liabilities' => 'reports.liabilities.pdf',
                        'reports.cashflow'    => 'reports.cashflow.pdf',
                        'tax.compliance'      => 'tax.compliance.pdf',
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
                    <button data-export-pdf class="border px-3 py-1.5 rounded text-sm font-medium hover:bg-gray-50 transition-colors">EXPORT PDF</button>
                @endif
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Admin User</p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-8">

            {{-- Report Tabs — only on the 4 report pages --}}
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
                        Assets
                    </a>
                    <a href="{{ route('reports.liabilities') }}"
                       class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors
                              {{ request()->routeIs('reports.liabilities') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                        Liabilities
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

{{-- ============ EXPORT SUCCESS MODAL ============ --}}
<div id="export-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-[420px] p-10 text-center">
        <div class="mx-auto mb-5 w-20 h-20 rounded-full border-2 border-slate-800 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-slate-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <p class="font-semibold text-xl mb-6">PDF EXPORTED!</p>
        <button onclick="closeExportModal()" class="bg-slate-900 text-white text-sm font-medium px-8 py-2.5 rounded hover:bg-slate-800 transition-colors">
            OKAY
        </button>
    </div>
</div>

<script>
    function openExportModal(e) {
        if (e) e.preventDefault();
        const modal = document.getElementById('export-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeExportModal() {
        const modal = document.getElementById('export-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-export-pdf]').forEach(function (btn) {
            btn.addEventListener('click', openExportModal);
        });
    });
</script>

@yield('scripts')
@stack('scripts')

</body>
</html>