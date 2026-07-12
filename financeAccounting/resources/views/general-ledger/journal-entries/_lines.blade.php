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
                    <th class="text-center text-xs font-semibold text-gray-500 uppercase pb-2" x-show="editingEntry">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(line, index) in displayLines" :key="index">
                    <tr class="border-b border-gray-100">
                        <template x-if="!editingEntry">
                            <td class="py-2 text-sm text-gray-900" x-text="line.account_name || line.account_code"></td>
                        </template>
                        <template x-if="editingEntry">
                            <td class="py-2">
                                <select x-model="line.account_id" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                    <option value="">Select account</option>
                                    <template x-for="acct in accounts" :key="acct.account_id">
                                        <option :value="acct.account_id" x-text="acct.account_code + ' - ' + acct.account_name"></option>
                                    </template>
                                </select>
                            </td>
                        </template>
                        <td class="py-2">
                            <input x-model="line.description" type="text" :readonly="!editingEntry"
                                class="w-full px-2 py-1 text-sm border rounded outline-none"
                                :class="editingEntry ? 'border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500' : 'border-transparent bg-transparent'">
                        </td>
                        <td class="py-2">
                            <input x-model="line.debit" type="number" step="0.01" min="0" :readonly="!editingEntry" @input="recalcTotals()"
                                class="w-full px-2 py-1 text-sm border rounded text-right outline-none"
                                :class="editingEntry ? 'border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500' : 'border-transparent bg-transparent'">
                        </td>
                        <td class="py-2">
                            <input x-model="line.credit" type="number" step="0.01" min="0" :readonly="!editingEntry" @input="recalcTotals()"
                                class="w-full px-2 py-1 text-sm border rounded text-right outline-none"
                                :class="editingEntry ? 'border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500' : 'border-transparent bg-transparent'">
                        </td>
                        <td class="py-2 text-center" x-show="editingEntry">
                            <button @click="removeLine(index)" class="text-red-500 hover:text-red-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>
                    </tr>
                </template>
                <tr x-show="displayLines.length === 0">
                    <td colspan="5" class="py-4 text-center text-sm text-gray-400">No lines. Click "Add Line" to add one.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <button x-show="editingEntry" @click="addLine()" class="w-full mt-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium flex items-center justify-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Line
    </button>
    <div class="flex justify-end gap-3 mt-4">
        <button @click="backToTable()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium text-sm">Back</button>
        <template x-if="!editingEntry">
            <button @click="startEdit()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium text-sm">Edit</button>
        </template>
        <template x-if="editingEntry">
            <button @click="cancelEdit()" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 font-medium text-sm">Cancel</button>
        </template>
        <template x-if="editingEntry">
            <button @click="saveDetails()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium text-sm">Save</button>
        </template>
    </div>
</div>
