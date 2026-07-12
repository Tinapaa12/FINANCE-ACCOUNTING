<div class="col-span-3 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
    <h3 class="text-base font-bold text-gray-900 mb-4">Journal Entry Details</h3>
    <div class="space-y-4">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Reference No.</label>
            <input x-model="editRef" type="text" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm" readonly>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
            <input x-model="editDate" type="date" :readonly="!editingEntry"
                class="w-full px-3 py-2 border rounded-lg text-sm outline-none"
                :class="editingEntry ? 'border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white' : 'border-gray-200 bg-gray-50'">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
            <textarea x-model="editDescription" rows="3" :readonly="!editingEntry"
                class="w-full px-3 py-2 border rounded-lg text-sm resize-none outline-none"
                :class="editingEntry ? 'border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white' : 'border-gray-200 bg-gray-50'"></textarea>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select x-model="editStatus" :disabled="!editingEntry"
                class="w-full px-3 py-2 border rounded-lg text-sm font-medium outline-none"
                :class="editingEntry ? 'border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white' : 'border-gray-200 bg-gray-50'">
                <option value="Draft">Draft</option>
                <option value="Posted">Posted</option>
            </select>
        </div>
    </div>
</div>
