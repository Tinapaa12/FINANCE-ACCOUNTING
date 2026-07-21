@extends('layouts.app')

@section('title', 'New Sales Transaction')
@section('page-title', 'New Sales Transaction')

@section('content')
<div class="max-w-2xl mx-auto">
    @if(session('success'))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="mx-auto mb-5 w-20 h-20 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Transaction Complete</h3>
            <p class="text-gray-600 mb-6">{{ session('success') }}</p>
            <a href="{{ route('sales-transactions.create') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Another Transaction
            </a>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">New Sales Transaction</h3>
                <a href="{{ route('ar.overview') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back
                </a>
            </div>

            <form method="POST" action="{{ route('sales-transactions.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name <span class="text-red-500">*</span></label>
                        <input name="customer_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="e.g. Juan Cruz" value="{{ old('customer_name') }}" required>
                        @error('customer_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount (₱) <span class="text-red-500">*</span></label>
                        <input name="total_amount" type="number" step="0.01" min="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="0.00" value="{{ old('total_amount') }}" required>
                        @error('total_amount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                        <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                            <option value="">Select payment method</option>
                            <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Credit Card" {{ old('payment_method') === 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="Bank Transfer" {{ old('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="Installment" {{ old('payment_method') === 'Installment' ? 'selected' : '' }}>Installment</option>
                        </select>
                        @error('payment_method') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                            <option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Paid" {{ old('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">If set to <strong>Paid</strong>, a journal entry will be automatically created in Finance.</p>
                        @error('status') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium">Create Transaction</button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection
