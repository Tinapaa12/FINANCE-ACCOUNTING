{{--
    resources/views/layouts/app.blade.php

    This is the master layout. Every report page extends this with:
        @extends('layouts.app')
        @section('title', 'Page Title')
        @section('content') ... @endsection
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Financial Reports')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; } ::-webkit-scrollbar-track { background: #0f172a; } ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        .content-scroll::-webkit-scrollbar-track { background: #f1f5f9; } .content-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; }
        .bg-dot-grid { background-image: radial-gradient(circle, #cbd5e1 1px, transparent 1px); background-size: 22px 22px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="flex h-screen overflow-hidden">

    {{-- ============ SIDEBAR ============ --}}
    <aside class="w-64 bg-slate-900 text-slate-300 flex-shrink-0 overflow-y-auto">
        <div class="flex items-center gap-3 px-5 py-6 border-b border-slate-800">
            <div class="bg-blue-600 text-white rounded-xl p-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 018 0v2m-4-9a4 4 0 100-8 4 4 0 000 8z" />
                </svg>
            </div>
            <span class="text-white font-bold text-lg leading-snug">Finance and<br>Accounting</span>
        </div>

        {{--
            NOTE on hover/active states:
            - Every link below has `transition-colors hover:bg-slate-800 hover:text-white`
              so hovering ANY link (even the currently active one) visibly reacts.
            - Active link additionally gets `bg-indigo-600 text-white` via the
              request()->routeIs(...) check, and we OVERRIDE the hover color for
              active links to a slightly darker indigo so it doesn't look broken on hover.
        --}}
        <nav class="px-3 py-6 space-y-7 text-[15px]">
            <div>
                <p class="uppercase text-xs font-medium tracking-wide text-slate-500 px-3 mb-2">Main</p>
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold transition-colors
                          {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'text-white hover:bg-slate-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4a1 1 0 001-1v-4h2v4a1 1 0 001 1h4a1 1 0 001-1V10" />
                    </svg>
                    Dashboard
                </a>
            </div>

            <div>
                <p class="uppercase text-xs font-medium tracking-wide text-slate-500 px-3 mb-2">General Ledger</p>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold text-white transition-colors hover:bg-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V9m3 8V5m3 12v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Chart of Accounts
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold text-white transition-colors hover:bg-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Journal Entries
                </a>
            </div>

            <div>
                <p class="uppercase text-xs font-medium tracking-wide text-slate-500 px-3 mb-2">Account Payables</p>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold text-white transition-colors hover:bg-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Supplier Bills
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold text-white transition-colors hover:bg-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="6" width="20" height="13" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 10h20M6 15h4" />
                    </svg>
                    Payments Made
                </a>
            </div>

            <div>
                <p class="uppercase text-xs font-medium tracking-wide text-slate-500 px-3 mb-2">Account Receivables</p>
                <a href="{{ route('ar.overview') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold transition-colors
                          {{ request()->routeIs('ar.overview') ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'text-white hover:bg-slate-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    A/R Overview
                </a>
                <a href="{{ route('ar.payments') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold transition-colors
                          {{ request()->routeIs('ar.payments') ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'text-white hover:bg-slate-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v2m9-8a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Payments Received
                </a>
                <a href="{{ route('ar.aging') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold transition-colors
                          {{ request()->routeIs('ar.aging') ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'text-white hover:bg-slate-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 018 0v2M12 3v4m-6 4h12M5 11h14a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2z" />
                    </svg>
                    Aging Report
                </a>
            </div>

            <div>
                <p class="uppercase text-xs font-medium tracking-wide text-slate-500 px-3 mb-2">Reports</p>
                <a href="{{ route('reports.income') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold transition-colors
                          {{ request()->routeIs('reports.*') ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'text-white hover:bg-slate-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l7 7-7 7zM3 5v14M9 19h9a2 2 0 002-2V7a2 2 0 00-2-2H9" />
                    </svg>
                    Financial Reports
                </a>
                <a href="{{ route('tax.compliance') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold transition-colors
                          {{ request()->routeIs('tax.compliance') ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'text-white hover:bg-slate-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Tax and Compliance
                </a>
            </div>
        </nav>
    </aside>

    {{-- ============ MAIN CONTENT ============ --}}
    <div class="flex-1 flex flex-col overflow-y-auto">

        {{-- Top bar --}}
        <header class="flex items-center justify-between bg-white px-6 py-4 border-b">
            <h1 class="text-xl font-semibold">@yield('page-heading', 'Financial Reports')</h1>
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
                @endif
                <button data-export-pdf class="border px-3 py-1.5 rounded text-sm font-medium hover:bg-gray-50 transition-colors">EXPORT PDF</button>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white">👤</div>
                    <div class="text-sm leading-tight">
                        <p class="font-medium">Admin User</p>
                        <p class="text-gray-400 text-xs">Administrator</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6">

            {{-- Report Tabs (Income Statements / Assets / Liabilities) — only shown on the 3 report pages --}}
            @if(request()->routeIs('reports.*'))
                <div class="inline-flex bg-white rounded-lg border p-1 mb-4">
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
    // Wire up every button marked data-export-pdf automatically
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-export-pdf]').forEach(function (btn) {
            btn.addEventListener('click', openExportModal);
        });
    });
</script>
@stack('scripts')

</body>
</html>
