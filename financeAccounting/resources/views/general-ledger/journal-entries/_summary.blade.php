<div class="col-span-3 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
    <h3 class="text-base font-bold text-gray-900 mb-4">Entry Summary</h3>
    <div class="space-y-3">
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Reference no.</span>
            <span class="font-medium text-gray-900" x-text="selectedEntry ? selectedEntry.reference : ''"></span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Status</span>
            <span :class="selectedEntry && selectedEntry.status === 'Posted' ? 'text-green-600' : 'text-gray-600'" class="font-medium" x-text="selectedEntry ? selectedEntry.status : ''"></span>
        </div>
        <div class="border-t border-gray-200 pt-3 mt-3">
            <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-600">Total Debit</span>
                <span class="font-bold text-gray-900" x-text="selectedEntry ? '₱' + Number(selectedEntry.debit).toLocaleString(undefined, {minimumFractionDigits: 2}) : ''"></span>
            </div>
            <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-600">Total Credit</span>
                <span class="font-bold text-gray-900" x-text="selectedEntry ? '₱' + Number(selectedEntry.credit).toLocaleString(undefined, {minimumFractionDigits: 2}) : ''"></span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Difference</span>
                <span class="font-bold" :class="selectedEntry && Number(selectedEntry.debit) === Number(selectedEntry.credit) ? 'text-green-600' : 'text-red-600'" x-text="selectedEntry ? '₱' + Math.abs(Number(selectedEntry.debit) - Number(selectedEntry.credit)).toLocaleString(undefined, {minimumFractionDigits: 2}) : ''"></span>
            </div>
            <div class="mt-3">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium" :class="selectedEntry && Number(selectedEntry.debit) === Number(selectedEntry.credit) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" x-text="selectedEntry && Number(selectedEntry.debit) === Number(selectedEntry.credit) ? 'Balanced' : 'Unbalanced'"></span>
            </div>
        </div>
        <div class="border-t border-gray-200 pt-3 mt-3 text-xs text-gray-500 space-y-1">
            <div class="flex justify-between">
                <span>Created at</span>
                <span x-text="selectedEntry ? selectedEntry.created_at : ''"></span>
            </div>
            <div class="flex justify-between">
                <span>Last Updated</span>
                <span x-text="selectedEntry ? selectedEntry.updated_at : ''"></span>
            </div>
        </div>
    </div>
</div>
