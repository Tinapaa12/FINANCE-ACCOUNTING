<div class="col-span-6 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
    <h3 class="text-base font-bold text-gray-900 mb-4">Journal Entry Lines</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase pb-2">Account</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase pb-2">Description</th>
                    <th class="text-right text-xs font-semibold text-gray-500 uppercase pb-2">Debit</th>
                    <th class="text-right text-xs font-semibold text-gray-500 uppercase pb-2">Credit</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(line, index) in displayLines" :key="index">
                    <tr class="border-b border-gray-100">
                        <td class="py-2 text-sm text-gray-900" x-text="line.account_name || line.account_code"></td>
                        <td class="py-2 text-sm text-gray-600" x-text="line.description"></td>
                        <td class="py-2 text-sm text-gray-900 text-right font-medium" x-text="'₱' + Number(line.debit).toLocaleString(undefined, {minimumFractionDigits: 2})"></td>
                        <td class="py-2 text-sm text-gray-900 text-right font-medium" x-text="'₱' + Number(line.credit).toLocaleString(undefined, {minimumFractionDigits: 2})"></td>
                    </tr>
                </template>
                <tr x-show="displayLines.length === 0">
                    <td colspan="4" class="py-4 text-center text-sm text-gray-400">No lines.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="flex justify-end gap-3 mt-4">
        <button @click="backToTable()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium text-sm">Back</button>
        <template x-if="editingEntry">
            <button @click="cancelEdit()" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 font-medium text-sm">Cancel</button>
        </template>
        <template x-if="editingEntry">
            <button @click="saveDetails()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium text-sm">Save</button>
        </template>
    </div>
</div>
