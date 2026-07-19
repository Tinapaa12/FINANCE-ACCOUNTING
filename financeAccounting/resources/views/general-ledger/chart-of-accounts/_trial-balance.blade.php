<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Trial Balance</h3>
    <div class="overflow-x-auto max-h-80 overflow-y-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left font-semibold text-gray-500 uppercase py-2 pr-2">Code</th>
                    <th class="text-left font-semibold text-gray-500 uppercase py-2 pr-2">Account</th>
                    <th class="text-right font-semibold text-gray-500 uppercase py-2 pl-2">Debit</th>
                    <th class="text-right font-semibold text-gray-500 uppercase py-2 pl-2">Credit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($trialBalance as $tb)
                <tr class="hover:bg-gray-50">
                    <td class="py-2 pr-2 text-gray-600">{{ $tb->account_code }}</td>
                    <td class="py-2 pr-2 text-gray-900 font-medium">{{ $tb->account_name }}</td>
                    <td class="py-2 pl-2 text-right {{ $tb->total_debits > 0 ? 'text-green-600' : 'text-gray-600' }}">{{ number_format($tb->total_debits, 2) }}</td>
                    <td class="py-2 pl-2 text-right {{ $tb->total_credits > 0 ? 'text-blue-600' : 'text-gray-600' }}">{{ number_format($tb->total_credits, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-6 text-center text-gray-400">No journal entry transactions yet.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-200 font-semibold">
                    <td colspan="2" class="py-2 text-gray-900">Totals</td>
                    <td class="py-2 pl-2 text-right text-green-700">{{ number_format($trialBalance->sum('total_debits'), 2) }}</td>
                    <td class="py-2 pl-2 text-right text-blue-700">{{ number_format($trialBalance->sum('total_credits'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>