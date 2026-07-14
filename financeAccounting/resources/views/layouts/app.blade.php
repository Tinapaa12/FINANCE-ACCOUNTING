<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Finance and Accounting')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .content-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .content-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; }
        [id$="Modal"].active{display:flex!important}
    </style>
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

                <x-sidebar-section title="General Ledger">
                    <x-sidebar-nav-item href="#" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'>Chart of Accounts</x-sidebar-nav-item>
                    <x-sidebar-nav-item href="#" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>'>Journal Entries</x-sidebar-nav-item>
                </x-sidebar-section>

                <x-sidebar-section title="Inventory">
                    <x-sidebar-nav-item
                        href="{{ route('inventory.index') }}"
                        :active="request()->routeIs('inventory.index') || request()->routeIs('inventory.store')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>'>
                        Inventory
                    </x-sidebar-nav-item>
                    <x-sidebar-nav-item
                        href="{{ route('inventory.tracking') }}"
                        :active="request()->routeIs('inventory.tracking*')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>'>
                        Inventory Tracking
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
                    <x-sidebar-nav-item href="#" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'>A/R Overview</x-sidebar-nav-item>
                    <x-sidebar-nav-item href="#" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>'>Payments Received</x-sidebar-nav-item>
                </x-sidebar-section>

            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Admin User</p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-8 content-scroll">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
        <div class="w-[500px] bg-white rounded-xl shadow-xl p-8" id="receiptContent">
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

    <script src="{{ asset('js/supplier-bills.js') }}"></script>
</body>
</html>
