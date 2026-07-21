<div class="p-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
        </div>
        <div>
            <h1 class="font-bold text-lg leading-tight">Manage Data</h1>
            <p class="text-xs text-slate-400">Manual Entry</p>
        </div>
    </div>
    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>

<nav class="flex-1 px-4 space-y-1 overflow-y-auto">
    <x-sidebar-section title="Financial Reports">
        <x-sidebar-nav-item
            href="{{ route('reports.manage', ['tab' => 'budget']) }}"
            :active="request()->routeIs('reports.manage') && request('tab', 'budget') === 'budget'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>'>
            Budget vs Actual
        </x-sidebar-nav-item>
        <x-sidebar-nav-item
            href="{{ route('reports.manage', ['tab' => 'tax']) }}"
            :active="request()->routeIs('reports.manage') && request('tab', 'tax') === 'tax'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-7-7-7 7M5 3h14M5 3v6a7 7 0 0014 0V3M5 3h14"></path>'>
            Tax Records
        </x-sidebar-nav-item>
    </x-sidebar-section>

    <x-sidebar-section title="Account Receivables">
        <x-sidebar-nav-item
            href="{{ route('sales-transactions.create') }}"
            :active="request()->routeIs('sales-transactions.*')"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'>
            Add Transaction
        </x-sidebar-nav-item>
    </x-sidebar-section>

    <x-sidebar-section title="Supply Management">
        <x-sidebar-nav-item
            href="{{ route('procurement.po.index') }}"
            :active="request()->routeIs('procurement.po.*')"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>'>
            Purchase Orders
        </x-sidebar-nav-item>
        <x-sidebar-nav-item
            href="{{ route('procurement.gr.index') }}"
            :active="request()->routeIs('procurement.gr.*')"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>'>
            Goods Receipts
        </x-sidebar-nav-item>
        <x-sidebar-nav-item
            href="{{ route('procurement.matching.index') }}"
            :active="request()->routeIs('procurement.matching.*')"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'>
            3-Way Matching
        </x-sidebar-nav-item>
    </x-sidebar-section>

    <x-sidebar-section title="Quick Links">
        <x-sidebar-nav-item
            href="{{ route('reports.budget') }}"
            :active="request()->routeIs('reports.budget')"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>'>
            View Budget Report
        </x-sidebar-nav-item>
        <x-sidebar-nav-item
            href="{{ route('dashboard') }}"
            :active="false"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>'>
            Back to Dashboard
        </x-sidebar-nav-item>
    </x-sidebar-section>
</nav>
