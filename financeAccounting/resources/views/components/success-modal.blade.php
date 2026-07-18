@props(['show' => false, 'title' => 'Added Successfully', 'buttonText' => 'Close'])

<div x-show="{{ $show }}" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="{{ $show }} = false"></div>
        <div class="relative bg-white rounded-2xl p-8 max-w-sm w-full text-center shadow-xl">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full border-2 border-gray-800 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $title }}</h3>
            <button @click="{{ $show }} = false" class="mt-4 px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium">{{ $buttonText }}</button>
        </div>
    </div>
</div>