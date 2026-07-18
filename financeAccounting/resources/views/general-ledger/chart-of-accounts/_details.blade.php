<div x-show="selectedAccount" x-transition class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Account Details</h3>
    <div class="grid grid-cols-2 gap-x-12 gap-y-4 max-w-3xl">
        @foreach([
            ['label' => 'Account Code', 'key' => 'code'],
            ['label' => 'Normal Balance', 'key' => 'normal_balance'],
            ['label' => 'Account Name', 'key' => 'name'],
            ['label' => 'Current Balance', 'key' => 'current_balance', 'currency' => true],
            ['label' => 'Type', 'key' => 'type'],
            ['label' => 'Status', 'key' => 'status'],
            ['label' => 'Date Created', 'key' => 'date_created'],
            ['label' => 'Last Updated', 'key' => 'last_updated'],
        ] as $field)
        <div class="flex justify-between">
            <span class="text-sm text-gray-600 font-medium">{{ $field['label'] }}</span>
            <span class="text-sm text-gray-900">:</span>
            <span class="text-sm text-gray-900 font-medium"
                  :class="'{{ $field['key'] }}' === 'current_balance' ? (selectedAccount && selectedAccount.current_balance >= 0 ? 'text-green-600' : 'text-red-600') : ''"
                  x-text="selectedAccount ? ('{{ $field['key'] }}' === 'current_balance' ? '₱' + selectedAccount.{{ $field['key'] }}.toLocaleString() : selectedAccount.{{ $field['key'] }}) : ''">
            </span>
        </div>
        @endforeach
    </div>
</div>
