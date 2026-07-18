<div class="w-[270px] min-h-screen bg-[#08182b] text-white">

    <div class="flex items-center gap-[15px] p-[20px] border-b border-white/10">
        <span class="text-[34px]">📄</span>
        <div>
            <h2 class="text-[24px]">Finance and</h2>
            <h2 class="text-[24px]">Accounting</h2>
        </div>
    </div>

    <div class="p-[18px]">

        <small class="block text-[#7d8ca2] mt-[22px] mb-[12px] text-[12px]">MAIN</small>

        <a href="{{ route('dashboard') }}" class="flex items-center gap-[12px] text-white no-underline p-[12px] rounded-[8px] mb-[6px] transition-colors duration-[.25s] hover:bg-[#1c3354] {{ request()->routeIs('dashboard') ? 'bg-[#4658e7]' : '' }}">
            <span>📊</span>
            Dashboard
        </a>

        <small class="block text-[#7d8ca2] mt-[22px] mb-[12px] text-[12px]">General Ledger</small>

        <a href="#" class="flex items-center gap-[12px] text-white no-underline p-[12px] rounded-[8px] mb-[6px] transition-colors duration-[.25s] hover:bg-[#1c3354]">
            <span>📒</span>
            Chart of Accounts
        </a>

        <a href="#" class="flex items-center gap-[12px] text-white no-underline p-[12px] rounded-[8px] mb-[6px] transition-colors duration-[.25s] hover:bg-[#1c3354]">
            <span>📝</span>
            Journal Entries
        </a>

        <small class="block text-[#7d8ca2] mt-[22px] mb-[12px] text-[12px]">Inventory</small>

        <a href="{{ route('inventory.index') }}" class="flex items-center gap-[12px] text-white no-underline p-[12px] rounded-[8px] mb-[6px] transition-colors duration-[.25s] hover:bg-[#1c3354] {{ request()->routeIs('inventory.index') || request()->routeIs('inventory.store') ? 'bg-[#4658e7]' : '' }}">
            <span>📦</span>
            Inventory
        </a>

        <a href="{{ route('inventory.tracking') }}" class="flex items-center gap-[12px] text-white no-underline p-[12px] rounded-[8px] mb-[6px] transition-colors duration-[.25s] hover:bg-[#1c3354] {{ request()->routeIs('inventory.tracking*') ? 'bg-[#4658e7]' : '' }}" style="padding-left:36px;font-size:13px;">
            <span>📋</span>
            Inventory Tracking
        </a>

        <small class="block text-[#7d8ca2] mt-[22px] mb-[12px] text-[12px]">Account Payables</small>

        <a href="{{ route('supplier-bills.index') }}" class="flex items-center gap-[12px] text-white no-underline p-[12px] rounded-[8px] mb-[6px] transition-colors duration-[.25s] hover:bg-[#1c3354] {{ request()->routeIs('supplier-bills*') ? 'bg-[#4658e7]' : '' }}">
            <span>🧾</span>
            Supplier Bills
        </a>

        <a href="{{ route('payments.index') }}" class="flex items-center gap-[12px] text-white no-underline p-[12px] rounded-[8px] mb-[6px] transition-colors duration-[.25s] hover:bg-[#1c3354] {{ request()->routeIs('payments*') ? 'bg-[#4658e7]' : '' }}">
            <span>💳</span>
            Payments Made
        </a>

        <small class="block text-[#7d8ca2] mt-[22px] mb-[12px] text-[12px]">Account Receivables</small>

        <a href="#" class="flex items-center gap-[12px] text-white no-underline p-[12px] rounded-[8px] mb-[6px] transition-colors duration-[.25s] hover:bg-[#1c3354]">
            <span>📄</span>
            A/R Overview
        </a>

        <a href="#" class="flex items-center gap-[12px] text-white no-underline p-[12px] rounded-[8px] mb-[6px] transition-colors duration-[.25s] hover:bg-[#1c3354]">
            <span>💰</span>
            Payments Received
        </a>

        <small class="block text-[#7d8ca2] mt-[22px] mb-[12px] text-[12px]">Reports</small>

        <a href="#" class="flex items-center gap-[12px] text-white no-underline p-[12px] rounded-[8px] mb-[6px] transition-colors duration-[.25s] hover:bg-[#1c3354]">
            <span>📈</span>
            Financial Reports
        </a>

        <a href="#" class="flex items-center gap-[12px] text-white no-underline p-[12px] rounded-[8px] mb-[6px] transition-colors duration-[.25s] hover:bg-[#1c3354]">
            <span>📋</span>
            Tax and Compliance
        </a>

    </div>

</div>
