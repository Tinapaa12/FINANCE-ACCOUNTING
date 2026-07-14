@extends('layouts.app')

@section('page-title', 'Payment Methods')

@section('content')

<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold">Payment Methods</h2>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors" onclick="openMethodModal()">Add Method</button>
    </div>
    <div class="p-6">
        <table class="w-full border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
@forelse($methods as $method)
<tr class="hover:bg-gray-50 transition-colors">
    <td class="px-4 py-4 border-t border-gray-100 text-sm">{{ $method->name }}</td>
    <td class="px-4 py-4 border-t border-gray-100 text-sm text-right">
        <div class="flex gap-2 justify-end">
            <button class="border-none cursor-pointer rounded-md bg-orange-500 text-white px-3 py-1.5 text-xs font-medium hover:bg-orange-600 transition-colors"
                onclick="editMethod({{ $method->id }}, '{{ $method->name }}')">Edit</button>
            <form action="{{ route('payment-methods.destroy', $method->id) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="border-none cursor-pointer rounded-md bg-red-500 text-white px-3 py-1.5 text-xs font-medium hover:bg-red-600 transition-colors"
                    onclick="return confirm('Delete this method?')">Delete</button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr><td colspan="2" class="text-center px-4 py-8 text-gray-500">No payment methods found.</td></tr>
@endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="methodModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
    <div class="w-[400px] bg-white rounded-xl shadow-xl p-8">
        <h2 class="text-xl font-bold mb-6">Add Payment Method</h2>
        <form method="POST" action="{{ route('payment-methods.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Method Name :</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeMethodModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Add</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editMethodModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50" style="display:none;">
    <div class="w-[400px] bg-white rounded-xl shadow-xl p-8">
        <h2 class="text-xl font-bold mb-6">Edit Payment Method</h2>
        <form id="editMethodForm" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Method Name :</label>
                <input type="text" name="name" id="editMethodName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeEditMethodModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openMethodModal() { document.getElementById('methodModal').classList.add('active'); }
function closeMethodModal() { document.getElementById('methodModal').classList.remove('active'); }
document.getElementById('methodModal')?.addEventListener('click', function(e) { if (e.target === this) closeMethodModal(); });
function editMethod(id, name) {
    document.getElementById('editMethodName').value = name;
    document.getElementById('editMethodForm').action = '/payment-methods/' + id;
    document.getElementById('editMethodModal').classList.add('active');
}
function closeEditMethodModal() { document.getElementById('editMethodModal').classList.remove('active'); }
document.getElementById('editMethodModal')?.addEventListener('click', function(e) { if (e.target === this) closeEditMethodModal(); });
</script>

@endsection
