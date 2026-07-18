<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div class="relative flex-1 max-w-md">
        <input type="text" x-model="searchQuery" placeholder="Search entries..."
            class="w-full pl-4 pr-12 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
        <svg class="w-5 h-5 text-gray-400 absolute right-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
    </div>
    <div class="flex items-center gap-2 sm:gap-3">
        <div class="relative">
            <select x-model="selectedMonth"
                class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white appearance-none cursor-pointer">
                <option value="">All Months</option>
                <template x-for="m in months" :key="m.value">
                    <option :value="m.value" x-text="m.label"></option>
                </template>
            </select>
            <svg class="w-4 h-4 text-gray-400 absolute right-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
        <button @click="openAddModal()" class="flex items-center gap-2 px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span class="hidden sm:inline">New Journal</span>
        </button>
    </div>
</div>

<x-filter-tabs active="filter" :options="['All' => 'All', 'Draft' => 'Drafts', 'Posted' => 'Posted']" />
