@props(['href' => '#', 'icon', 'active' => false])

<a href="{{ $href }}"
   class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ $active ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {!! $icon !!}
    </svg>
    <span>{{ $slot }}</span>
</a>
