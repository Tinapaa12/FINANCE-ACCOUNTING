@props(['href' => '#', 'active' => false, 'icon' => ''])

<a href="{{ $href }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
          {{ $active ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
    <span>{{ $slot }}</span>
</a>
