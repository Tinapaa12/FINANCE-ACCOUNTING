@props(['active', 'options'])

<div class="flex gap-2 mb-4">
    @foreach($options as $value => $label)
        <button 
            @click="{{ $active }} = '{{ $value }}'" 
            :class="{{ $active }} === '{{ $value }}' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
            class="px-5 py-2 rounded-lg font-medium text-sm transition-colors">
            {{ $label }}
        </button>
    @endforeach
</div>